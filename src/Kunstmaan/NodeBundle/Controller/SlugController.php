<?php

namespace Kunstmaan\ViewBundle\Controller;

use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;

use Kunstmaan\AdminBundle\Entity\PageIFace;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SlugController extends Controller
{
	/**
	 * @Route("/draft/{slug}", requirements={"slug" = ".+"}, name="_slug_draft")
	 * @Template("KunstmaanViewBundle:Slug:slug.html.twig")
	 */
	public function slugDraftAction($slug)
	{
		$em = $this->getDoctrine()->getEntityManager();
		$node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeForSlug(null, $slug);
		if($node){
			$page = $node->getRef($em);
			$page = $page = $em->getRepository('KunstmaanAdminBundle:DraftConnector')->getDraft($page);
			$nodeMenu = new NodeMenu($this->container, $node);
			//3. render page
			$pageparts = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page);
			return array(
					'page' => $page,
					'pageparts' => $pageparts,
					'nodemenu' => $nodeMenu);
		} else {
			throw $this->createNotFoundException('No page found for slug ' . $slug);
		}
	}
	
    /**
     * @Route("/{slug}", requirements={"slug" = ".+"}, name="_slug")
     * @Template()
     */
    public function slugAction($slug)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeForSlug(null, $slug);
    	if($node){
            $page = $node->getRef($em);
    	} else {
    		throw $this->createNotFoundException('No page found for slug ' . $slug);
    	}

        //check if the requested node is online, else throw a 404 exception
        if(!$node->isOnline()){
            throw $this->createNotFoundException("The requested page is not online");
        }

        $currentUser = $this->get('security.context')->getToken()->getUser();

        $permissionManager = $this->get('kunstmaan_admin.permissionmanager');
        $canViewPage = $permissionManager->hasPermision($page, $currentUser, 'read', $em);

        if($canViewPage) {
            $nodeMenu = new NodeMenu($this->container, $node);

        	//render page
        	$pageparts = $em->getRepository('KunstmaanPagePartBundle:PagePartRef')->getPageParts($page);
            return array(
                'page'      => $page,
                'pageparts' => $pageparts,
                'nodemenu'  => $nodeMenu);
        }
        throw $this->createNotFoundException('You do not have suffucient rights to access this page.');
    }
    
    
}
