<?php

namespace Kunstmaan\AdminBundle\Controller;

use Kunstmaan\AdminBundle\Modules\PrepersistListener;
use Doctrine\ORM\Events;
use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\AdminBundle\Form\PageAdminType;
use Kunstmaan\AdminBundle\Entity\PageIFace;
use Kunstmaan\AdminBundle\AdminList\PageAdminListConfigurator;
use Kunstmaan\DemoBundle\PagePartAdmin\PagePartAdminConfigurator;
use Kunstmaan\PagePartBundle\Form\TextPagePartAdminType;
use Kunstmaan\AdminBundle\Form\NodeInfoAdminType;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\AdminBundle\Entity\Permission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class PagesController extends Controller
{
	/**
	 * @Route("/", name="KunstmaanAdminBundle_pages")
	 * @Template()
	 */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $user = $this->container->get('security.context')->getToken()->getUser();
        $topnodes = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($user, 'write');
        $nodeMenu = new NodeMenu($this->container, null, 'write');

        $request    = $this->getRequest();
        $adminlist  = $this->get("adminlist.factory")->createList(new PageAdminListConfigurator($user, 'write'), $em);
        $adminlist->bindRequest($request);

        return array(
			'topnodes'      => $topnodes,
        	'nodemenu' 	    => $nodeMenu,
            'pageadminlist' => $adminlist,
        );
    }

/**
     * @Route("/{id}/publish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminBundle_pages_edit_publish")
     * @Template()
     */
    public function publishAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
    	$node->setOnline(true);
    	$em->persist($node);
    	$em->flush();
    	return $this->redirect($this->generateUrl("KunstmaanAdminBundle_pages_edit", array('id'=>$node->getId())));
    }
    
    /**
     * @Route("/{id}/unpublish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminBundle_pages_edit_unpublish")
     * @Template()
     */
    public function unpublishAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
    	$node->setOnline(false);
    	$em->persist($node);
    	$em->flush();
    	return $this->redirect($this->generateUrl("KunstmaanAdminBundle_pages_edit", array('id'=>$node->getId())));
    }
    
    /**
     * @Route("/{id}/{subaction}", requirements={"_method" = "GET|POST", "id" = "\d+"}, defaults={"subaction" = "public"}, name="KunstmaanAdminBundle_pages_edit")
     * @Template()
     */
    public function editAction($id, $subaction)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$request = $this->getRequest();
    	$locale = $request->getSession()->getLocale();
    	$saveasdraft = $request->get("saveasdraft");
    	$saveandpublish = $request->get("saveandpublish");
    	$draft = ($subaction == "draft");
        
        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
        $page = $em->getRepository($node->getRefEntityname())->find($node->getRefId());
        
        if($draft){
        	$page = $em->getRepository('KunstmaanAdminBundle:DraftConnector')->getDraft($page);
        } else if(is_string($saveasdraft) && $saveasdraft != ''){
        	$newpublicpage = $em->getRepository('KunstmaanAdminBundle:DraftConnector')->saveAsDraftAndReturnPublish($page);
        	$draft = true;
        	$subaction = "draft";
        }
        
        $addpage = $request->get("addpage");
        if(is_string($addpage) && $addpage != ''){
        	$newpage = new $addpage();
        	if(is_string($addpagetitle) && $addpagetitle != ''){
        		$newpage->setTitle($addpagetitle);
        	} else {
        		$newpage->setTitle('New page');
        	}
        	$newpage->setTranslatableLocale($locale);
        	$em->persist($newpage);
        	$em->flush();

        	$nodeparent = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($page);
            $nodenewpage = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($newpage);
            $nodenewpage->setParent($nodeparent);

            //get permissions of the parent and apply them on the new child
            $parentPermissions = $em->getRepository('KunstmaanAdminBundle:Permission')->findBy(array(
                'refId'             => $nodeparent->getId(),
                'refEntityname'     => $nodeparent->getRefEntityname(),
            ));

            if(count($parentPermissions)) {
                foreach($parentPermissions as $parentPermission) {
                    $permission = new Permission();

                    $permission->setRefId($nodenewpage->getId());
                    $permission->setPermissions($parentPermission->getPermissions());
                    $permission->setRefEntityname($nodenewpage->getRefEntityname());
                    $permission->setRefGroup($parentPermission->getRefGroup());

                    $em->persist($permission);
                    $em->flush();
                }
            }

        	$em->persist($nodenewpage);
        	$em->flush();

        	return $this->redirect($this->generateUrl("KunstmaanAdminBundle_pages_edit", array('id'=>$nodenewpage->getId())));
        }

        $delete = $request->get("delete");
        if(is_string($delete) && $delete == 'true'){
        	//remove node and page
        	$nodeparent = $node->getParent();
        	$em->remove($page);
        	$em->flush();
        	return $this->redirect($this->generateUrl("KunstmaanAdminBundle_pages_edit", array('id'=>$nodeparent->getId())));
        }

		//$page = $em->getRepository(ClassLookup::getClass($page))->find($id);  //'KunstmaanAdminBundle:Page'
        $page->setTranslatableLocale($locale);
        $em->refresh($page);
        $repo = $em->getRepository('StofDoctrineExtensionsBundle:LogEntry');
        $logs = $repo->getLogEntries($page);
        if(!is_null($this->getRequest()->get('version'))) {
        	$repo->revert($page, $this->getRequest()->get('version'));
        }
        $user = $this->container->get('security.context')->getToken()->getUser();
        $topnodes   = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($user, 'write');
        //$node       = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($page);

        $formfactory = $this->container->get('form.factory');
        $formbuilder = $this->createFormBuilder();

        //add the specific data from the custom page
        $formbuilder->add('main', $page->getDefaultAdminType());
        $formbuilder->add('node', $node->getDefaultAdminType($this->container));

        $formbuilder->setData(array('node' => $node, 'main' => $page));

        //handle the pagepart functions (fetching, change form to reflect all fields, assigning data, etc...)
        $pagepartadmin = $this->get("pagepartadmin.factory")->createList(new PagePartAdminConfigurator(), $em, $page, 'main', $this->container);
        $pagepartadmin->preBindRequest($request);
        $pagepartadmin->adaptForm($formbuilder, $formfactory);

        if ($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')) {
            $permissionadmin = $this->get("kunstmaan_admin.permissionadmin");
            $permissionadmin->initialize($page, $em);
        }

        $form = $formbuilder->getForm();
        if ($request->getMethod() == 'POST') {
            $form           ->bindRequest($request);
            $pagepartadmin  ->bindRequest($request);

            if ($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')) {
                $permissionadmin->bindRequest($request);
            }

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                $formValues = $request->request->get('form');
                if(isset($formValues['node']['roles'])) {
                    $roles = array_keys($formValues['node']['roles']);
                } else {
                    $roles = array();
                }

                $node->setRoles($roles);

                $em->persist($node);
                $em->persist($page);
                $em->flush();

                if(is_string($saveandpublish) && $saveandpublish != ''){
                	$newpublicpage = $em->getRepository('KunstmaanAdminBundle:DraftConnector')->copyDraftToPublishedReturnPublished($page);
                	$draft = false;
                	$subaction = "public";
                }

                return $this->redirect($this->generateUrl('KunstmaanAdminBundle_pages_edit', array(
                    'id' => $node->getId(),
                	'subaction' => $subaction
                )));
            }
        }

        $nodeMenu = new NodeMenu($this->container, $node, 'write');

        $viewVariables = array(
            'topnodes'          => $topnodes,
            'page'              => $page,
            'entityname'        => ClassLookup::getClass($page),
            'form'              => $form->createView(),
            'pagepartadmin'     => $pagepartadmin,
            'logs'              => $logs,
            'nodemenu'          => $nodeMenu,
            'node'              => $node,
        	'draft'             => $draft,
        	'subaction'         => $subaction
        );
        if($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')){
            $viewVariables['permissionadmin'] = $permissionadmin;
        }
        return $viewVariables;
    }
	
}
