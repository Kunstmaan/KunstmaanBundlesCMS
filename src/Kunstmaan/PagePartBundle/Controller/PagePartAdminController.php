<?php

namespace Kunstmaan\PagePartBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\PagePartBundle\Entity\PagePartRef;
use Kunstmaan\PagePartBundle\Repository\PagePartRefRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Kunstmaan\AdminNodeBundle\Helper\NodeMenu;

class PagePartAdminController extends Controller
{

    public function indexAction()
    {
        return $this->render('KunstmaanPagePartBundle:PagePartAdmin:index.html.twig');
    }

    /**
     * Moves a PagePartRef in a certain direction.
     *
     * @param integer $id       the id of the pagepartref
     * @param integer $steps    amount of steps to move, 1 for one up, -1 for one down
     */
    private function movePagePartRef($id, $steps)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var PagePartRefRepository $repo  */
        $repo = $em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        /** @var PagePartRef $pagepartref  */
        $pagepartref = $repo->find($id);
        /** @var PagePartRef[] $pagepartrefs  */
        $pagepartrefs = $repo->findBy(array('context' => $pagepartref->getContext(),
                                            'pageId' => $pagepartref->getPageId(),
                                            'pageEntityname' => $pagepartref->getPageEntityName()));
        foreach ($pagepartrefs as &$ppref) {
            if ($ppref->getSequenceNumber() + $steps == $pagepartref->getSequenceNumber()) {
                $ppref->setSequenceNumber($pagepartref->getSequenceNumber());
                $em->persist($ppref);
            }
        }
        if ($pagepartref->getSequenceNumber() > 1) {
            $pagepartref->setSequenceNumber($pagepartref->getSequenceNumber() - 1);
            $em->persist($pagepartref);
        }
        $em->flush();
    }

    /**
     * @param integer $id
     */
    public function moveUpAction($id)
    {
        $this->movePagePartRef($id, 1);
    }

    /**
     * @param integer $id
     */
    public function moveDownAction($id)
    {
       $this->movePagePartRef($id, -1);
    }

    /**
     * @Route   ("/pageparts/selecturl", name="KunstmaanPagePartBundle_selecturl")
     * @Template()
     */
    public function selectlinkAction()
    {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $locale = $request->getLocale();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $topnodes = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($locale, $user, 'read', true);
        $nodeMenu = new NodeMenu($this->container, $locale, null, 'read', false, true);

        return array('topnodes' => $topnodes, 'nodemenu' => $nodeMenu,);
    }
}
