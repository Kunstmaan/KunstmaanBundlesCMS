<?php

namespace Kunstmaan\PagePartBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Kunstmaan\AdminNodeBundle\Helper\NodeMenu;

class PagePartAdminController extends Controller
{

    /**
     * Index
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('KunstmaanPagePartBundle:PagePartAdmin:index.html.twig');
    }

    /**
     * Moves a PagePartRef in a certain direction.
     *
     * @param integer $id    the id of the pagepartref
     * @param integer $steps amount of steps to move, 1 for one up, -1 for one down
     */
    private function movePagePartRef($id, $steps)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PagePartRefRepository $repo  */
        $repo = $em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        /** @var PagePartRef $pagePartRef  */
        $pagePartRef = $repo->find($id);
        /** @var PagePartRef[] $pagePartRefs  */
        $pagePartRefs = $repo->findBy(array('context' => $pagePartRef->getContext(),
                                            'pageId' => $pagePartRef->getPageId(),
                                            'pageEntityname' => $pagePartRef->getPageEntityName()));
        foreach ($pagePartRefs as &$ppRef) {
            if ($ppRef->getSequenceNumber() + $steps == $pagePartRef->getSequenceNumber()) {
                $ppRef->setSequenceNumber($pagePartRef->getSequenceNumber());
                $em->persist($ppRef);
            }
        }
        if ($pagePartRef->getSequenceNumber() > 1) {
            $pagePartRef->setSequenceNumber($pagePartRef->getSequenceNumber() - 1);
            $em->persist($pagePartRef);
        }
        $em->flush();
    }

    /**
     * Move a page part up
     *
     * @param int $id
     */
    public function moveUpAction($id)
    {
        $this->movePagePartRef($id, 1);
    }

    /**
     * Move a page part down
     *
     * @param int $id
     */
    public function moveDownAction($id)
    {
       $this->movePagePartRef($id, -1);
    }

    /**
     * Select a link
     *
     * @Route   ("/pageparts/selecturl", name="KunstmaanPagePartBundle_selecturl")
     * @Template()
     */
    public function selectLinkAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $locale = $request->getLocale();
        $securityContext = $this->container->get('security.context');
        $aclHelper = $this->container->get('kunstmaan.acl.helper');
        $topNodes = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, PermissionMap::PERMISSION_VIEW, $aclHelper, true);
        $nodeMenu = new NodeMenu($em, $securityContext, $aclHelper, $locale, null, PermissionMap::PERMISSION_VIEW, false, true);

        return array('topnodes' => $topNodes, 'nodemenu' => $nodeMenu);
    }
}
