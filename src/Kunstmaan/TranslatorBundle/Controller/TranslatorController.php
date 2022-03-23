<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AbstractAdminListController;
use Kunstmaan\TranslatorBundle\AdminList\TranslationAdminListConfigurator;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Form\TranslationAdminType;
use Kunstmaan\TranslatorBundle\Form\TranslationsFileUploadType;
use Kunstmaan\TranslatorBundle\Service\Command\Importer\Importer;
use Kunstmaan\TranslatorBundle\Service\Translator\CacheValidator;
use Kunstmaan\UtilitiesBundle\Helper\SlugifierInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TranslatorController extends AbstractAdminListController
{
    /** @var AbstractAdminListConfigurator */
    private $adminListConfigurator;
    /** @var CacheValidator */
    private $cacheValidator;
    /** @var Importer */
    private $importerService;
    /** @var SlugifierInterface */
    private $slugifier;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(CacheValidator $cacheValidator, Importer $importerService, SlugifierInterface $slugifier, EntityManagerInterface $em)
    {
        $this->cacheValidator = $cacheValidator;
        $this->importerService = $importerService;
        $this->slugifier = $slugifier;
        $this->em = $em;
    }

    /**
     * @Route("/", name="KunstmaanTranslatorBundle_settings_translations")
     */
    public function indexAction(Request $request): Response
    {
        $configurator = $this->getAdminListConfigurator();

        /* @var AdminList $adminList */
        $adminList = $this->container->get('kunstmaan_adminlist.factory')->createList($configurator);
        $adminList->bindRequest($request);

        $cacheFresh = $this->cacheValidator->isCacheFresh();
        $debugMode = $this->getParameter('kuma_translator.debug') === true;

        if (!$cacheFresh && !$debugMode) {
            $this->addFlash(
                FlashTypes::INFO,
                $this->container->get('translator')->trans('settings.translator.not_live_warning')
            );
        }

        return $this->render('@KunstmaanTranslator/Translator/list.html.twig', [
            'adminlist' => $adminList,
            'adminlistconfigurator' => $configurator,
        ]);
    }

    /**
     * @param string $keyword
     * @param string $domain
     * @param string $locale
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/add", name="KunstmaanTranslatorBundle_settings_translations_add", methods={"GET", "POST"})
     */
    public function addAction(Request $request, $keyword = '', $domain = '', $locale = ''): Response
    {
        $configurator = $this->getAdminListConfigurator();
        $translator = $this->container->get('translator');

        $translation = new \Kunstmaan\TranslatorBundle\Model\Translation();
        $managedLocales = $this->getParameter('kuma_translator.managed_locales');
        foreach ($managedLocales as $managedLocale) {
            $translation->addText($managedLocale, '');
        }

        $form = $this->createForm(TranslationAdminType::class, $translation, ['csrf_token_id' => 'add']);
        if ($request->getMethod() === Request::METHOD_POST) {
            $form->handleRequest($request);

            // Fetch form data
            $data = $form->getData();
            if (!$this->em->getRepository(Translation::class)->isUnique($data)) {
                $error = new FormError($translator->trans('translator.translation_not_unique'));
                $form->get('domain')->addError($error);
                $form->get('keyword')->addError($error);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                // Create translation
                $this->em->getRepository(Translation::class)->createTranslations($data);
                $this->em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->container->get('translator')->trans('settings.translator.succesful_added')
                );

                $indexUrl = $configurator->getIndexUrl();

                return $this->redirectToRoute($indexUrl['path'], $indexUrl['params'] ?? []);
            }
        }

        return $this->render('@KunstmaanTranslator/Translator/addTranslation.html.twig', [
            'form' => $form->createView(),
            'adminlistconfigurator' => $configurator,
        ]);
    }

    /**
     * The edit action
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_edit", methods={"GET", "POST"})
     *
     * @param $id
     *
     * @throws \InvalidArgumentException
     */
    public function editAction(Request $request, $id): Response
    {
        $configurator = $this->getAdminListConfigurator();

        $translations = $this->em->getRepository(Translation::class)->findBy(['translationId' => $id]);
        if (\count($translations) < 1) {
            throw new \InvalidArgumentException('No existing translations found for this id');
        }

        $translation = new \Kunstmaan\TranslatorBundle\Model\Translation();
        $translation->setDomain($translations[0]->getDomain());
        $translation->setKeyword($translations[0]->getKeyword());
        $locales = $this->getParameter('kuma_translator.managed_locales');
        foreach ($locales as $locale) {
            $found = false;
            foreach ($translations as $t) {
                if ($locale == $t->getLocale()) {
                    $translation->addText($locale, $t->getText(), $t->getId());
                    $found = true;
                }
            }
            if (!$found) {
                $translation->addText($locale, '');
            }
        }

        $form = $this->createForm(TranslationAdminType::class, $translation, ['intention' => 'edit']);

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Update translations
                $this->em->getRepository(Translation::class)->updateTranslations($translation, $id);
                $this->em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->container->get('translator')->trans('settings.translator.succesful_edited')
                );

                $indexUrl = $configurator->getIndexUrl();

                return $this->redirectToRoute($indexUrl['path'], $indexUrl['params'] ?? []);
            }
        }

        return $this->render('@KunstmaanTranslator/Translator/editTranslation.html.twig', [
            'form' => $form->createView(),
            'translation' => $translation,
            'adminlistconfigurator' => $configurator,
        ]);
    }

    /**
     * @Route("upload", name="KunstmaanTranslatorBundle_settings_translations_upload", methods={"GET", "POST"})
     */
    public function uploadFileAction(Request $request): Response
    {
        /** @var FormBuilderInterface $uploadForm */
        $form = $this->createForm(TranslationsFileUploadType::class);
        $configurator = $this->getAdminListConfigurator();

        if (Request::METHOD_POST === $request->getMethod()) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $locales = $this->getParameter('kuma_translator.managed_locales');
                $data = $form->getData();
                $file = $data['file'];
                $force = $data['force'];
                $imported = $this->importerService->importFromSpreadsheet($file, $locales, $force);
                $this->addFlash(FlashTypes::SUCCESS, sprintf('Translation imported: %d', $imported));
            }
        }

        return $this->render('@KunstmaanTranslator/Translator/addTranslation.html.twig', [
            'form' => $form->createView(),
            'adminlistconfigurator' => $configurator,
        ]);
    }

    /**
     * @param string $_format
     *
     * @Route("/export.{_format}", requirements={"_format" = "csv|ods|xlsx"}, name="KunstmaanTranslatorBundle_settings_translations_export", methods={"GET", "POST"})
     */
    public function exportAction(Request $request, $_format): Response
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }

    /**
     * @param $domain
     * @param $locale
     * @param $keyword
     */
    public function editSearchAction($domain, $locale, $keyword): RedirectResponse
    {
        $configurator = $this->getAdminListConfigurator();
        $translation = $this->em->getRepository(Translation::class)->findOneBy(
            ['domain' => $domain, 'keyword' => $keyword, 'locale' => $locale]
        );

        if ($translation === null) {
            $addUrl = $configurator->getAddUrlFor(
                ['domain' => $domain, 'keyword' => $keyword, 'locale' => $locale]
            );

            return $this->redirectToRoute($addUrl['path'], $addUrl['params']);
        }

        $editUrl = $configurator->getEditUrlFor(['id' => $translation->getId()]);

        return $this->redirectToRoute($editUrl['path'], $editUrl['params']);
    }

    /**
     * @param $id
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_delete", methods={"POST"})
     */
    public function deleteAction(Request $request, $id): RedirectResponse
    {
        $indexUrl = $this->getAdminListConfigurator()->getIndexUrl();
        if (!$this->isCsrfTokenValid('delete-' . $this->slugifier->slugify($this->getAdminListConfigurator()->getEntityName()), $request->request->get('token'))) {
            return $this->redirectToRoute($indexUrl['path'], $indexUrl['params'] ?? []);
        }

        if ($request->isMethod('POST')) {
            $this->em->getRepository(Translation::class)->removeTranslations($id);
        }

        return $this->redirectToRoute($indexUrl['path'], $indexUrl['params'] ?? []);
    }

    /**
     * @param $adminListConfigurator
     */
    public function setAdminListConfigurator($adminListConfigurator)
    {
        $this->adminListConfigurator = $adminListConfigurator;
    }

    /**
     * @return AbstractAdminListConfigurator
     */
    public function getAdminListConfigurator()
    {
        $locales = $this->getParameter('kuma_translator.managed_locales');

        if (!isset($this->adminListConfigurator)) {
            $this->adminListConfigurator = new TranslationAdminListConfigurator($this->em->getConnection(), $locales);
        }

        return $this->adminListConfigurator;
    }

    /**
     * @Route("/inline-edit", name="KunstmaanTranslatorBundle_settings_translations_inline_edit", methods={"POST"})
     *
     * @return JsonResponse|Response
     */
    public function inlineEditAction(Request $request)
    {
        $values = $request->request->all();

        $adminListConfigurator = $this->getAdminListConfigurator();
        if (!$adminListConfigurator->canEditInline($values)) {
            throw $this->createAccessDeniedException('Not allowed to edit this translation');
        }

        $id = isset($values['pk']) ? (int) $values['pk'] : 0;
        $translator = $this->container->get('translator');

        try {
            if ($id !== 0) {
                // Find existing translation
                $translation = $this->em->getRepository(Translation::class)->find($id);

                if (\is_null($translation)) {
                    return new Response($translator->trans('translator.translator.invalid_translation'), 500);
                }
            } else {
                // Create new translation
                $translation = new Translation();
                $translation->setDomain($values['domain']);
                $translation->setKeyword($values['keyword']);
                $translation->setLocale($values['locale']);
                $translation->setTranslationId($values['translationId']);
            }
            $translation->setText($values['value']);
            $this->em->persist($translation);
            $this->em->flush();

            return new JsonResponse(['success' => true, 'uid' => $translation->getId()], 200);
        } catch (\Exception $e) {
            return new Response($translator->trans('translator.translator.fatal_error_occurred'), 500);
        }
    }
}
