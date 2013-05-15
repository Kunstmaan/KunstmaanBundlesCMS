<?php

namespace Kunstmaan\SitemapBundle\Controller;


use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class SitemapController extends Controller {

    /**
     * Use the mode parameter to select in which mode the sitemap should be generated. At this moment only XML is supported
     *
     * @Route("/sitemap.{mode}", name="KunstmaanSitemapBundle_sitemap")
     * @Template("KunstmaanSitemapBundle:Sitemap:view.xml.twig")
     *
     * @param $mode
     *
     * @return array
     */
    public function sitemapAction($mode)
    {
        $em = $this->getDoctrine()->getManager();
        $locale = $this->getRequest()->getLocale();
        $securityContext = $this->container->get('security.context');
        $aclHelper = $this->container->get('kunstmaan_admin.acl.helper');
        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, null, PermissionMap::PERMISSION_VIEW, true, true);

        return array(
            'nodemenu' => $nodeMenu,
        );
    }

}