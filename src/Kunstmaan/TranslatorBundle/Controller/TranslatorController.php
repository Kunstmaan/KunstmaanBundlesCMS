<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Kunstmaan\AdminListBundle\AdminList\Configurator\AbstractAdminListConfigurator;
use Kunstmaan\TranslatorBundle\AdminList\TranslationAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\TranslatorBundle\Form\TranslationAdminType;
use Kunstmaan\TranslatorBundle\Entity\Translation;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        $debugMode = $this->container->getParameter('kernel.debug') === true;

        if(!$cacheFresh && !$debugMode) {
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
     * @Method({"GET", "POST"})
     * @Template("KunstmaanTranslatorBundle:Translator:addTranslation.html.twig")
     * @return array
     */
    public function addAction($keyword = '', $domain = '', $locale = '')
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $configurator = $this->getAdminListConfigurator();

        $translation = new Translation();
        $translation->setDomain($domain);
        $translation->setKeyword($keyword);
        $translation->setLocale($locale);

        $locales = $this->container->getParameter('kuma_translator.managed_locales');

        $choicesText = $this->get('translator')->trans('settings.translator.succesful_added');
        $form = $this->createForm(new TranslationAdminType(), $translation);
        $form->add('locale','language', array('choices' => array_combine($locales, $locales), 'empty_value' => $choicesText));
        $form->add('domain','text');
        $form->add('keyword','text');


        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($translation);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('settings.translator.succesful_added'));

                $indexUrl = $configurator->getIndexUrl();
                return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
            }
        }

        return array(
            'form' => $form->createView(),
            'adminlistconfigurator' => $configurator
        );
    }

    /**
     * @param $id
     *
     * @throws NotFoundHttpException
     * @internal param $eid
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     * @Route("/{id}/edit", requirements={"id" = "\d+"}, name="KunstmaanTranslatorBundle_settings_translations_edit")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanTranslatorBundle:Translator:editTranslation.html.twig")
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $configurator = $this->getAdminListConfigurator();

        $translation = $em->getRepository('KunstmaanTranslatorBundle:Translation')->find($id);
        $form = $this->createForm(new TranslationAdminType(), $translation);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {

                $em->persist($translation);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', $this->get('translator')->trans('settings.translator.succesful_edited'));

                $indexUrl = $configurator->getIndexUrl();
                return new RedirectResponse($this->generateUrl($indexUrl['path'], isset($indexUrl['params']) ? $indexUrl['params'] : array()));
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
        $translation = $em->getRepository('KunstmaanTranslatorBundle:Translation')->findOneBy(array('domain' => $domain, 'keyword' => $keyword, 'locale' => $locale));

        if ($translation == null) {
            $addUrl = $configurator->getAddUrlFor(array('domain' => $domain, 'keyword' => $keyword, 'locale' => $locale));
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
    public function deleteAction($id)
    {
        return parent::doDeleteAction($this->getAdminListConfigurator(), $id);
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
        if (!isset($this->adminListConfigurator)) {
            $this->adminListConfigurator = new TranslationAdminListConfigurator($this->getDoctrine()->getManager());
        }

        return $this->adminListConfigurator;
    }

}
