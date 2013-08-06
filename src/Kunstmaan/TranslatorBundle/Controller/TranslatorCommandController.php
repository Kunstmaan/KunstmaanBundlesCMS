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

        return array(
            'adminlist' => $adminList,
        );


        return new RedirectResponse($this->generateUrl('KunstmaanTranslatorBundle_settings_translations'));
    }


}
