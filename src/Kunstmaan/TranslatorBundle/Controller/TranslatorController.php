<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\TranslatorBundle\AdminList\TranslationAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\TranslatorBundle\Form\TranslationAdminType;
use Kunstmaan\TranslatorBundle\Entity\Translation;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


class TranslatorController extends AdminListController
{

    /**
     * @var AbstractAdminListConfigurator
     */
    private $adminListConfigurator;


    /**
     * @Route("/", name="KunstmaanTranslatorBundle_settings_translations")
     * @Template("KunstmaanTranslatorBundle:Translator:list.html.twig")
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $configurator = $this->getAdminListConfigurator();

        /* @var AdminList $adminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList($configurator);
        $adminList->bindRequest($request);

        $cacheFresh = $this->get('kunstmaan_translator.service.translator.cache_validator')->isCacheFresh();
        $debugMode = $this->container->getParameter('kuma_translator.debug') === true;

        if (!$cacheFresh && !$debugMode) {
            $noticeText = $this->get('translator')->trans('settings.translator.not_live_warning');
            $this->get('session')->getFlashBag()->add('notice', $noticeText);
        }

        return array(
          'adminlist' => $adminList,
          'adminlistconfigurator' => $configurator
        );
    }

    /**
     * The add action
     *
     * @Route("/add", name="KunstmaanTranslatorBundle_settings_translations_add")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanTranslatorBundle:Translator:addTranslation.html.twig")
     *
     * @param string $keyword
     * @param string $domain
     * @param string $locale
     * @return array|RedirectResponse
     */
    public function addAction($keyword = '', $domain = '', $locale = '')
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $configurator = $this->getAdminListConfigurator();
        $translator = $this->get('translator');

        $translation = new \Kunstmaan\TranslatorBundle\Model\Translation();
        $locales = $this->container->getParameter('kuma_translator.managed_locales');
        foreach ($locales as $locale) {
            $translation->addText($locale, '');
        }

        $form = $this->createForm(new TranslationAdminType('add'), $translation);
        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            // Fetch form data
            $data = $form->getData();
            if (!$em->getRepository('KunstmaanTranslatorBundle:Translation')->isUnique($data)) {
                $error = new FormError($translator->trans('translator.translation_not_unique'));
                $form->get('domain')->addError($error);
                $form->get('keyword')->addError($error);
            }

            if ($form->isValid()) {
                // Create translation
                $em->getRepository('KunstmaanTranslatorBundle:Translation')->createTranslations($data);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                  'success',
                  $this->get('translator')->trans('settings.translator.succesful_added')
                );

                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse($this->generateUrl(
                    $indexUrl['path'],
                    isset($indexUrl['params']) ? $indexUrl['params'] : array()
                ));
            }
        }

        return array(
            'form' => $form->createView(),
            'adminlistconfigurator' => $configurator
        );
    }

    /**
     * The edit action
     *
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_edit")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanTranslatorBundle:Translator:editTranslation.html.twig")
     *
     * @param $id
     * @throws \InvalidArgumentException
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $configurator = $this->getAdminListConfigurator();


        $translations = $em->getRepository('KunstmaanTranslatorBundle:Translation')->findBy(array('translationId' => $id));
        if (count($translations) < 1) {
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

        $form = $this->createForm(new TranslationAdminType('edit'), $translation);

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                // Update translations
                $em->getRepository('KunstmaanTranslatorBundle:Translation')->updateTranslations($translation, $id);
                $em->flush();

                $this->get('session')->getFlashBag()->add(
                  'success',
                  $this->get('translator')->trans('settings.translator.succesful_edited')
                );

                $indexUrl = $configurator->getIndexUrl();

                return new RedirectResponse($this->generateUrl(
                    $indexUrl['path'],
                    isset($indexUrl['params']) ? $indexUrl['params'] : array()
                ));
            }
        }

        return array(
            'form' => $form->createView(),
            'translation' => $translation,
            'adminlistconfigurator' => $configurator
        );
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

        return new RedirectResponse($this->generateUrl($editUrl['path'], $editUrl['params']));
    }

    /**
     * @param $id
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws NotFoundHttpException
     * @Route("/{id}/delete", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_delete")
     * @Method({"GET", "POST"})
     */
    public function deleteAction(Request $request, $id)
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();

        $indexUrl = $this->getAdminListConfigurator()->getIndexUrl();
        if ($request->isMethod('POST')) {
            $em->getRepository('KunstmaanTranslatorBundle:Translation')->removeTranslations($id);
        }

        return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
    }

    public function setAdminListConfigurator($adminListConfigurator)
    {
        $this->adminListConfigurator = $adminListConfigurator;
    }

    /**
     * @return AbstractAdminListConfigurator
     */
    public function getAdminListConfigurator()
    {
        $locales = explode('|', $this->container->getParameter('requiredlocales'));

        if (!isset($this->adminListConfigurator)) {
            $this->adminListConfigurator = new TranslationAdminListConfigurator($this->getDoctrine()->getManager()
              ->getConnection(), $locales);
        }

        return $this->adminListConfigurator;
    }

    /**
     * @Route("/inline-edit", name="KunstmaanTranslatorBundle_settings_translations_inline_edit")
     * @Method({"POST"})
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
        /**
         * @var Translator $translator
         */
        $translator = $this->get('translator');

        try {
            if ($id !== 0) {
                // Find existing translation
                $translation = $em->getRepository('KunstmaanTranslatorBundle:Translation')->find($id);

                if (is_null($translation)) {
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

            return new JsonResponse(array(
              'success' => true,
              'uid' => $translation->getId()
            ), 200);
        } catch (\Exception $e) {
            return new Response($translator->trans('translator.translator.fatal_error_occurred'), 500);
        }
    }
}
