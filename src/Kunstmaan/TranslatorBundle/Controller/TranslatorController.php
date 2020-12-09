<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\TranslatorBundle\AdminList\TranslationAdminListConfigurator;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Form\TranslationAdminType;
use Kunstmaan\TranslatorBundle\Form\TranslationsFileUploadType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Translation\TranslatorInterface;

class TranslatorController extends AdminListController
{
    /**
     * @var AbstractAdminListConfigurator
     */
    private $adminListConfigurator;

    /**
     * @Route("/", name="KunstmaanTranslatorBundle_settings_translations")
     * @Template("@KunstmaanTranslator/Translator/list.html.twig")
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $configurator = $this->getAdminListConfigurator();

        /* @var AdminList $adminList */
        $adminList = $this->container->get('kunstmaan_adminlist.factory')->createList($configurator);
        $adminList->bindRequest($request);

        $cacheFresh = $this->container->get('kunstmaan_translator.service.translator.cache_validator')->isCacheFresh();
        $debugMode = $this->container->getParameter('kuma_translator.debug') === true;

        if (!$cacheFresh && !$debugMode) {
            $this->addFlash(
                FlashTypes::INFO,
                $this->container->get('translator')->trans('settings.translator.not_live_warning')
            );
        }

        return [
            'adminlist' => $adminList,
            'adminlistconfigurator' => $configurator,
        ];
    }

    /**
     * @param string $keyword
     * @param string $domain
     * @param string $locale
     *
     * @return array|RedirectResponse
     *
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route("/add", name="KunstmaanTranslatorBundle_settings_translations_add", methods={"GET", "POST"})
     * @Template("@KunstmaanTranslator/Translator/addTranslation.html.twig")
     */
    public function addAction(Request $request, $keyword = '', $domain = '', $locale = '')
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $configurator = $this->getAdminListConfigurator();
        $translator = $this->container->get('translator');

        $translation = new \Kunstmaan\TranslatorBundle\Model\Translation();
        $managedLocales = $this->container->getParameter('kuma_translator.managed_locales');
        foreach ($managedLocales as $managedLocale) {
            $translation->addText($managedLocale, '');
        }

        $form = $this->createForm(TranslationAdminType::class, $translation, ['csrf_token_id' => 'add']);
        if ($request->getMethod() === Request::METHOD_POST) {
            $form->handleRequest($request);

            // Fetch form data
            $data = $form->getData();
            if (!$em->getRepository(Translation::class)->isUnique($data)) {
                $error = new FormError($translator->trans('translator.translation_not_unique'));
                $form->get('domain')->addError($error);
                $form->get('keyword')->addError($error);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                // Create translation
                $em->getRepository(Translation::class)->createTranslations($data);
                $em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->container->get('translator')->trans('settings.translator.succesful_added')
                );

                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse($this->generateUrl(
                    $indexUrl['path'],
                    isset($indexUrl['params']) ? $indexUrl['params'] : []
                ));
            }
        }

        return [
            'form' => $form->createView(),
            'adminlistconfigurator' => $configurator,
        ];
    }

    /**
     * The edit action
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_edit", methods={"GET", "POST"})
     * @Template("@KunstmaanTranslator/Translator/editTranslation.html.twig")
     *
     * @param $id
     *
     * @throws \InvalidArgumentException
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $configurator = $this->getAdminListConfigurator();

        $translations = $em->getRepository(Translation::class)->findBy(['translationId' => $id]);
        if (\count($translations) < 1) {
            throw new \InvalidArgumentException('No existing translations found for this id');
        }

        $translation = new \Kunstmaan\TranslatorBundle\Model\Translation();
        $translation->setDomain($translations[0]->getDomain());
        $translation->setKeyword($translations[0]->getKeyword());
        $locales = $this->container->getParameter('kuma_translator.managed_locales');
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
                $em->getRepository(Translation::class)->updateTranslations($translation, $id);
                $em->flush();

                $this->addFlash(
                    FlashTypes::SUCCESS,
                    $this->container->get('translator')->trans('settings.translator.succesful_edited')
                );

                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse($this->generateUrl(
                    $indexUrl['path'],
                    isset($indexUrl['params']) ? $indexUrl['params'] : []
                ));
            }
        }

        return [
            'form' => $form->createView(),
            'translation' => $translation,
            'adminlistconfigurator' => $configurator,
        ];
    }

    /**
     * @Route("upload", name="KunstmaanTranslatorBundle_settings_translations_upload", methods={"GET", "POST"})
     * @Template("@KunstmaanTranslator/Translator/addTranslation.html.twig")
     *
     * @return array
     */
    public function uploadFileAction(Request $request)
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
                $imported = $this->container->get('kunstmaan_translator.service.importer.importer')->importFromSpreadsheet($file, $locales, $force);
                $this->addFlash(FlashTypes::SUCCESS, sprintf('Translation imported: %d', $imported));
            }
        }

        return [
            'form' => $form->createView(),
            'adminlistconfigurator' => $configurator,
        ];
    }

    /**
     * The export action
     *
     * @param string $_format
     *
     * @Route("/export.{_format}", requirements={"_format" = "csv|ods|xlsx"}, name="KunstmaanTranslatorBundle_settings_translations_export", methods={"GET", "POST"})
     *
     * @return array
     */
    public function exportAction(Request $request, $_format)
    {
        return parent::doExportAction($this->getAdminListConfigurator(), $_format, $request);
    }

    /**
     * @param $domain
     * @param $locale
     * @param $keyword
     *
     * @return RedirectResponse
     */
    public function editSearchAction($domain, $locale, $keyword)
    {
        $configurator = $this->getAdminListConfigurator();
        $em = $this->getDoctrine()->getManager();
        $translation = $em->getRepository(Translation::class)->findOneBy(
            ['domain' => $domain, 'keyword' => $keyword, 'locale' => $locale]
        );

        if ($translation === null) {
            $addUrl = $configurator->getAddUrlFor(
                ['domain' => $domain, 'keyword' => $keyword, 'locale' => $locale]
            );

            return new RedirectResponse($this->generateUrl($addUrl['path'], $addUrl['params']));
        }

        $editUrl = $configurator->getEditUrlFor(['id' => $translation->getId()]);

        return new RedirectResponse($this->generateUrl($editUrl['path'], $editUrl['params']));
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws NotFoundHttpException
     *
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_delete", methods={"GET", "POST"})
     */
    public function deleteAction(Request $request, $id)
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $indexUrl = $this->getAdminListConfigurator()->getIndexUrl();
        if ($request->isMethod('POST')) {
            $em->getRepository(Translation::class)->removeTranslations($id);
        }

        return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : []));
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
        $locales = $this->container->getParameter('kuma_translator.managed_locales');

        if (!isset($this->adminListConfigurator)) {
            $this->adminListConfigurator = new TranslationAdminListConfigurator($this->getDoctrine()->getConnection(), $locales);
        }

        return $this->adminListConfigurator;
    }

    /**
     * @return JsonResponse|Response
     *
     * @Route("/inline-edit", name="KunstmaanTranslatorBundle_settings_translations_inline_edit", methods={"POST"})
     */
    public function inlineEditAction(Request $request)
    {
        $values = $request->request->all();

        $adminListConfigurator = $this->getAdminListConfigurator();
        if (!$adminListConfigurator->canEditInline($values)) {
            throw $this->createAccessDeniedException('Not allowed to edit this translation');
        }

        $id = isset($values['pk']) ? (int) $values['pk'] : 0;
        $em = $this->getDoctrine()->getManager();
        /**
         * @var TranslatorInterface
         */
        $translator = $this->container->get('translator');

        try {
            if ($id !== 0) {
                // Find existing translation
                $translation = $em->getRepository(Translation::class)->find($id);

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
            $em->persist($translation);
            $em->flush();

            return new JsonResponse([
                'success' => true,
                'uid' => $translation->getId(),
            ], 200);
        } catch (\Exception $e) {
            return new Response($translator->trans('translator.translator.fatal_error_occurred'), 500);
        }
    }
}
