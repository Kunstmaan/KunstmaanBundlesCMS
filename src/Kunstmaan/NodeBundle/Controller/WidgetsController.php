<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;

use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * WidgetsController
 */
class WidgetsController extends Controller
{

    /**
     * @Route("/ckselecturl", name="KunstmaanNodeBundle_ckselecturl")
     * @Template()
     *
     * @return array
     */
    public function ckSelectLinkAction()
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $locale = $this->getRequest()->getLocale();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        /* @var AclHelper $aclHelper */
        $aclHelper = $this->container->get('kunstmaan_admin.acl.helper');

        $topNodes = $em->getRepository('KunstmaanNodeBundle:Node')->getTopNodes($locale, PermissionMap::PERMISSION_VIEW, $aclHelper, true);
        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, null, PermissionMap::PERMISSION_VIEW, true, true);

        return array(
            'topnodes' => $topNodes,
            'nodemenu' => $nodeMenu,
        );
    }

    /**
     * Select a link
     *
     * @Route   ("/selecturl", name="KunstmaanNodeBundle_selecturl")
     * @Template()
     *
     * @return array
     */
    public function selectLinkAction()
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $locale = $request->getLocale();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        /* @var AclHelper $aclHelper */
        $aclHelper = $this->container->get('kunstmaan_admin.acl.helper');

        $topNodes = $em->getRepository('KunstmaanNodeBundle:Node')->getTopNodes($locale, PermissionMap::PERMISSION_VIEW, $aclHelper, true);
        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, null, PermissionMap::PERMISSION_VIEW, true, true);

        return array(
            'topnodes' => $topNodes,
            'nodemenu' => $nodeMenu
        );
    }

}
