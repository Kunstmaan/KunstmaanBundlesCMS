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
     * @Method({"GET", "POST"})
     * @Template("KunstmaanAdminListBundle:Default:add.html.twig")
     * @return array
     */
    public function addAction()
    {
        return parent::doAddAction($this->getAdminListConfigurator());
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

    /**
     * @Route("/all", name="KunstmaanAdminBundle_settings_translations")
     * @Template("KunstmaanAdminListBundle:Default:list.html.twig")
     */
    public function settings()
    {
        return parent::doIndexAction($this->getAdminListConfigurator());
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
