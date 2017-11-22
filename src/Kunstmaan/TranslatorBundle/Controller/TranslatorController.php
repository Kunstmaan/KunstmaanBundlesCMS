<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Exception;
use InvalidArgumentException;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\AdminListBundle\AdminList\AdminList;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\TranslatorBundle\AdminList\TranslationAdminListConfigurator;
use Kunstmaan\TranslatorBundle\Model\Translation as TranslationModel;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Kunstmaan\TranslatorBundle\Form\TranslationAdminType;
use Kunstmaan\TranslatorBundle\Repository\TranslationRepository;
use Kunstmaan\TranslatorBundle\Service\Exception\TranslationsNotFoundException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class TranslatorController
 * @package Kunstmaan\TranslatorBundle\Controller
 */
class TranslatorController extends AdminListController
{

    /**
     * @var TranslationAdminListConfigurator
     */
    private $adminListConfigurator;

    /** @var ObjectManager $em */
    private $em;

    /** @var TranslationRepository $repo */
    private $repo;

    /**
     * TranslatorController constructor.
     */
    public function __construct()
    {
        $this->em = $this->getDoctrine()->getManager();
        $this->repo = $this->em->getRepository('KunstmaanTranslatorBundle:Translation');
    }


    /**
     * @Route("/", name="KunstmaanTranslatorBundle_settings_translations")
     * @Template("KunstmaanTranslatorBundle:Translator:list.html.twig")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $configurator = $this->getAdminListConfigurator();

        /* @var AdminList $adminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList($configurator);
        $adminList->bindRequest($request);

        $cacheFresh = $this->get('kunstmaan_translator.service.translator.cache_validator')->isCacheFresh();
        $debugMode = $this->getParameter('kuma_translator.debug') === true;

        if (!$cacheFresh && !$debugMode) {
            $msg = $this->get('translator')->trans('settings.translator.not_live_warning');
            $this->addFlash(FlashTypes::INFO, $msg);
        }

        return [
            'adminlist' => $adminList,
            'adminlistconfigurator' => $configurator
        ];
    }

    /**
     * The add action
     *
     * @Route("/add", name="KunstmaanTranslatorBundle_settings_translations_add")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanTranslatorBundle:Translator:addTranslation.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param string $keyword
     * @param string $domain
     * @param string $locale
     *
     * @return array|RedirectResponse
     */
    public function addAction(Request $request, $keyword = '', $domain = '', $locale = '')
    {
        $configurator = $this->getAdminListConfigurator();
        $translator = $this->get('translator');
        $translation = new TranslationModel();
        $this->addLocales($translation);
        $form = $this->createForm(TranslationAdminType::class, $translation, array('csrf_token_id' => 'add'));
        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);
            $data = $form->getData();
            if (!$this->repo->isUnique($data)) {
                $error = new FormError($translator->trans('translator.translation_not_unique'));
                $form->get('domain')->addError($error);
                $form->get('keyword')->addError($error);
            }

            if ($form->isSubmitted() && $form->isValid()) {
                $this->repo->createTranslations($data);
                $this->em->flush();
                $msg = $this->get('translator')->trans('settings.translator.succesful_added');
                $this->addFlash(FlashTypes::SUCCESS, $msg);
                $indexUrl = $configurator->getIndexUrl();
                $params = isset($indexUrl['params']) ? $indexUrl['params'] : [];
                $url = $this->generateUrl($indexUrl['path'], $params);

                return new RedirectResponse($url);
            }
        }

        return ['form' => $form->createView(), 'adminlistconfigurator' => $configurator];
    }

    /**
     * @param TranslationModel $translation
     */
    private function addLocales(TranslationModel $translation)
    {
        $locales = $this->getParameter('kuma_translator.managed_locales');
        foreach ($locales as $locale) {
            $translation->addText($locale, '');
        }
    }

    /**
     * The edit action
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_edit")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanTranslatorBundle:Translator:editTranslation.html.twig")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param $id
     * @throws \InvalidArgumentException
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction(Request $request, $id)
    {
        $configurator = $this->getAdminListConfigurator();
        $translations = $this->getTranslationsById($id);
        $translation = $this->getTranslationToEdit($translations);
        $form = $this->createForm(TranslationAdminType::class, $translation, array('intention' => 'edit'));

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->repo->updateTranslations($translation, $id);
                $this->em->flush();
                $msg = $this->get('translator')->trans('settings.translator.succesful_edited');
                $this->addFlash(FlashTypes::SUCCESS, $msg);
                $indexUrl = $configurator->getIndexUrl();
                $params = isset($indexUrl['params']) ? $indexUrl['params'] : [];
                $url = $this->generateUrl($indexUrl['path'], $params);

                return new RedirectResponse($url);
            }
        }

        return ['form' => $form->createView(), 'translation' => $translation, 'adminlistconfigurator' => $configurator];
    }

    /**
     * @param $id
     *
     * @return Translation[]
     */
    private function getTranslationsById($id)
    {
        /** @var Translation[] $translations */
        $translations = $this->repo->findBy(array('translationId' => $id));
        if (count($translations) < 1) {
            throw new InvalidArgumentException('No existing translations found for this id');
        }
        return $translations;
    }

    /**
     * @param array $translations
     *
     * @return TranslationModel
     */
    private function getTranslationToEdit(array $translations)
    {
        $translation = new TranslationModel();
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
        return $translation;
    }

    /**
     * @Method({"GET"})
     */
    public function editSearchAction($domain, $locale, $keyword)
    {
        $configurator = $this->getAdminListConfigurator();
        $em = $this->getDoctrine()->getManager();
        $translation = $em->getRepository('KunstmaanTranslatorBundle:Translation')->findOneBy(
          array('domain' => $domain, 'keyword' => $keyword, 'locale' => $locale)
        );

        if ($translation === null) {
            $addUrl = $configurator->getAddUrlFor(
              array('domain' => $domain, 'keyword' => $keyword, 'locale' => $locale)
            );

            return new RedirectResponse($this->generateUrl($addUrl['path'], $addUrl['params']));
        }

        $editUrl = $configurator->getEditUrlFor(array('id' => $translation->getId()));
        $url = $this->generateUrl($editUrl['path'], $editUrl['params']);

        return new RedirectResponse($url);
    }

    /**
     * @param $id
     * @throws NotFoundHttpException
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_delete")
     * @Method({"GET", "POST"})
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, $id)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        $indexUrl = $this->getAdminListConfigurator()->getIndexUrl();
        if ($request->isMethod('POST')) {
            $em->getRepository('KunstmaanTranslatorBundle:Translation')->removeTranslations($id);
        }
        $params = isset($indexUrl['params']) ? $indexUrl['params'] : [];
        $url = $this->generateUrl($indexUrl['path'], $params);

        return new RedirectResponse($url);
    }

    /**
     * @param $adminListConfigurator
     */
    public function setAdminListConfigurator($adminListConfigurator)
    {
        $this->adminListConfigurator = $adminListConfigurator;
    }

    /**
     * @return TranslationAdminListConfigurator
     */
    protected function getAdminListConfigurator()
    {
        $locales = explode('|', $this->getParameter('requiredlocales'));

        if (!isset($this->adminListConfigurator)) {
            $this->adminListConfigurator = new TranslationAdminListConfigurator($this->getDoctrine()->getManager()
              ->getConnection(), $locales);
        }

        return $this->adminListConfigurator;
    }

    /**
     * @Route("/inline-edit", name="KunstmaanTranslatorBundle_settings_translations_inline_edit")
     * @Method({"POST"})
     * @param Request $request
     *
     * @return JsonResponse|Response
     */
    public function inlineEditAction(Request $request)
    {
        $values = $request->request->all();

        $adminListConfigurator = $this->getAdminListConfigurator();
        if (!$adminListConfigurator->canEditInline($values)) {
            throw new AccessDeniedHttpException("Not allowed to edit this translation");
        }

        $id = isset($values['pk']) ? (int) $values['pk'] : 0;
        $em = $this->getDoctrine()->getManager();
        $translator = $this->get('translator');

        try {
            $translation = $this->getInlineEditTranslation($id, $translator, $values);
            $em->persist($translation);
            $em->flush();

            return new JsonResponse(['success' => true, 'uid' => $translation->getId()], 200);
        } catch (TranslationsNotFoundException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        } catch (Exception $e) {
            return new Response($translator->trans('translator.translator.fatal_error_occurred'), 500);
        }
    }

    /**
     * @param $id
     * @param TranslatorInterface $translator
     * @param array $values
     * @throws TranslationsNotFoundException
     * 
     * @return Translation|null|object
     */
    private function getInlineEditTranslation($id, TranslatorInterface $translator, array $values)
    {
        if ($id !== 0) {
            // Find existing translation
            $translation = $this->em->getRepository('KunstmaanTranslatorBundle:Translation')->find($id);

            if (is_null($translation)) {
                throw new TranslationsNotFoundException($translator->trans('translator.translator.invalid_translation'), 404);
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

        return $translation;
    }
}
