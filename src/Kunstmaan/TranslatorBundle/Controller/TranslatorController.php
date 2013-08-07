<?php

namespace Kunstmaan\TranslatorBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Kunstmaan\TranslatorBundle\AdminList\TranslationAdminListConfigurator;
use Kunstmaan\AdminListBundle\Controller\AdminListController;
use Kunstmaan\TranslatorBundle\Form\TranslationAdminType;
use Kunstmaan\TranslatorBundle\Entity\Translation;

class TranslatorController extends AdminListController
{

    /**
     * @var AdminListConfiguratorInterface
     */
    private $adminListConfigurator;


    /**
     * @Route("/", name="KunstmaanTranslatorBundle_settings_translations")
     * @Template("KunstmaanTranslatorBundle:Translator:list.html.twig")
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        /* @var AdminList $adminList */
        $adminList = $this->get("kunstmaan_adminlist.factory")->createList($this->getAdminListConfigurator());
        $adminList->bindRequest($request);

        $cacheFresh = $this->get('kunstmaan_translator.service.translator.cache_validator')->isCacheFresh();
        $debugMode = $this->container->getParameter('kernel.debug') === true;

        if(!$cacheFresh && !$debugMode) {
            $this->get('session')->getFlashBag()->add('notice', "Translations on the live website aren't up to date, hit 'Refresh live' to update to the latest translations.");
        }

        return array(
            'adminlist' => $adminList,
        );
    }

    /**
     * The add action
     *
     * @Route("/add", name="KunstmaanTranslatorBundle_settings_translations_add")
     * @Route("/add/{domain}/{locale}/{keyword}", name="KunstmaanTranslatorBundle_settings_translations_add")
     * @Method({"GET", "POST"})
     * @Template("KunstmaanTranslatorBundle:Translator:addTranslation.html.twig")
     * @return array
     */
    public function addAction($keyword = '', $domain = '', $locale = '')
    {
        /* @var $em EntityManager */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $translation = new Translation();
        $translation->setDomain($domain);
        $translation->setKeyword($keyword);
        $translation->setLocale($locale);

        $locales = $this->container->getParameter('kuma_translator.managed_locales');

        $form = $this->createForm(new TranslationAdminType(), $translation);
        $form->add('locale','language', array('choices' => array_combine($locales, $locales), 'empty_value' => 'Choose a language'));
        $form->add('domain','text');
        $form->add('keyword','text');


        if ('POST' == $request->getMethod()) {
            $form->bind($request);
            if ($form->isValid()) {
                $em->persist($translation);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'Translation succesful created');

                return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
            }
        }

        return array(
            'form' => $form->createView(),
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

        $translation = $em->getRepository('KunstmaanTranslatorBundle:Translation')->find($id);
        $form = $this->createForm(new TranslationAdminType(), $translation);

        if ('POST' == $request->getMethod()) {
            $form->bind($request);

            if ($form->isValid()) {

                $em->persist($translation);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', 'Translation has been edited!');

                return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
            }
        }

        return array(
            'form' => $form->createView(),
            'translation' => $translation
        );
    }

    /**
     * @Route("/edit/{domain}/{locale}/{keyword}", name="KunstmaanTranslatorBundle_settings_translations_edit_search")
     * @Method({"GET"})
     */
    public function editSearchAction($domain, $locale, $keyword)
    {
        $em = $this->getDoctrine()->getManager();
        $translation = $em->getRepository('KunstmaanTranslatorBundle:Translation')->findOneBy(array('domain' => $domain, 'keyword' => $keyword, 'locale' => $locale));
        return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations_edit', array('id' => $translation->getId())));
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
     * @return AdminListConfiguratorInterface
     */
    public function getAdminListConfigurator()
    {
        if (!isset($this->adminListConfigurator)) {
            $this->adminListConfigurator = new TranslationAdminListConfigurator($this->getDoctrine()->getManager());
        }

        return $this->adminListConfigurator;
    }

}
