<?php

namespace Kunstmaan\PagePartBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class PagePartAdminController extends Controller
{
    
    public function indexAction() {
        return $this->render('KunstmaanPagePartBundle:PagePartAdmin:index.html.twig');
    }

    public function moveUpAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $pagepartref = $repo->find($id);
        $pagepartrefs = $repo->findBy(array('context' => $pagepartref->getContext(), 'pageId' => $pagepartref->getPageId(), 'pageEntityname' => $pagepartref->getPageEntityName()));
        foreach ($pagepartrefs as &$ppref){
            if($ppref->getSequenceNumber()+1==$pagepartref->getSequenceNumber()){
                $ppref->setSequenceNumber($pagepartref->getSequenceNumber());
                $em->persist($ppref);
            }
        }
        if($pagepartref->getSequenceNumber()>1){
            $pagepartref->setSequenceNumber($pagepartref->getSequenceNumber()-1);
            $em->persist($pagepartref);
        }
        $em->flush();
    }

    public function moveDownAction($id) {
        $em = $this->getDoctrine()->getEntityManager();
        $repo = $em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $pagepartref = $repo->find($id);
        $pagepartrefs = $repo->findBy(array('context' => $pagepartref->getContext(), 'pageId' => $pagepartref->getPageId(), 'pageEntityname' => $pagepartref->getPageEntityName()));
        foreach ($pagepartrefs as &$ppref){
            if($ppref->getSequenceNumber()-1==$pagepartref->getSequenceNumber()){
                $ppref->setSequenceNumber($pagepartref->getSequenceNumber());
                $em->persist($ppref);
            }
        }
        if($pagepartref->getSequenceNumber()<count($pagepartrefs)){
            $pagepartref->setSequenceNumber($pagepartref->getSequenceNumber()+1);
            $em->persist($pagepartref);
        }
        $em->flush();
    }
}
