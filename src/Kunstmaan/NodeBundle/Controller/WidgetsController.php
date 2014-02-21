<?php

namespace Kunstmaan\NodeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * WidgetsController
 */
class WidgetsController extends Controller
{
    /**
     * @Route("/ckselecturl", name="KunstmaanNodeBundle_ckselecturl")
     * @Template("KunstmaanNodeBundle:Widgets:selectLink.html.twig")
     *
     * @return array
     */
    public function ckSelectLinkAction()
    {
        $params = $this->getTemplateParameters();
        $params['cke'] = true;

        return $params;
    }

    /**
     * Select a link
     *
     * @Route   ("/selecturl", name="KunstmaanNodeBundle_selecturl")
     * @Template("KunstmaanNodeBundle:Widgets:selectLink.html.twig")
     *
     * @return array
     */
    public function selectLinkAction()
    {
        $params = $this->getTemplateParameters();
        $params['cke'] = false;

        return $params;
    }

    /**
     * Get the parameters needed in the template. This is common for the default link chooser and the cke link chooser.
     *
     * @return array
     */
    private function getTemplateParameters()
    {
        /* @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $locale = $this->getRequest()->getLocale();
        /* @var SecurityContextInterface $securityContext */
        $securityContext = $this->container->get('security.context');
        /* @var AclHelper $aclHelper */
        $aclHelper = $this->container->get('kunstmaan_admin.acl.helper');

        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, null, PermissionMap::PERMISSION_VIEW, true, true);

        // When the media bundle is available, we show a link in the header to the media chooser
        $allBundles = $this->container->getParameter('kernel.bundles');
        if (array_key_exists('KunstmaanMediaBundle', $allBundles)) {
            $params = array('linkChooser' => 1);
            $cKEditorFuncNum = $this->getRequest()->get('CKEditorFuncNum');
            if (!empty($cKEditorFuncNum)) {
                $params['CKEditorFuncNum'] = $cKEditorFuncNum;
            }
            $mediaChooserLink = $this->generateUrl('KunstmaanMediaBundle_chooser', $params);
        }

        return array(
            'nodemenu' => $nodeMenu,
            'mediaChooserLink' => $mediaChooserLink
        );
    }
}
