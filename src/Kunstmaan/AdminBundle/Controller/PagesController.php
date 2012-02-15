<?php

namespace Kunstmaan\AdminBundle\Controller;

use Kunstmaan\AdminBundle\Modules\PrepersistListener;
use Doctrine\ORM\Events;
use Kunstmaan\AdminNodeBundle\Modules\NodeMenu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\AdminBundle\Form\PageAdminType;
use Kunstmaan\AdminBundle\Entity\PageIFace;
use Kunstmaan\AdminBundle\AdminList\PageAdminListConfigurator;
use Kunstmaan\PagePartBundle\Form\TextPagePartAdminType;
use Kunstmaan\AdminBundle\Form\NodeInfoAdminType;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Kunstmaan\AdminBundle\Entity\Permission;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Kunstmaan\AdminBundle\Modules\Slugifier;
use Kunstmaan\AdminNodeBundle\Form\SEOType;

class PagesController extends Controller
{
	/**
	 * @Route("/", name="KunstmaanAdminBundle_pages")
	 * @Template()
	 */
    public function indexAction() {
        $em = $this->getDoctrine()->getEntityManager();
        $request = $this->getRequest();
        $locale = $request->getSession()->getLocale();
        $user = $this->container->get('security.context')->getToken()->getUser();
        $topnodes = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($user, 'write');
        $nodeMenu = new NodeMenu($this->container, $locale, null, 'write');

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
     * @Route("/copyfromotherlanguage/{id}/{otherlanguage}", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminBundle_pages_copyfromotherlanguage")
     * @Template()
     */
    public function copyFromOtherLanguageAction($id, $otherlanguage)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$request = $this->getRequest();
    	$locale = $request->getSession()->getLocale();
    	$node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
    	$otherLanguageNodeTranslation = $node->getNodeTranslation($otherlanguage);
    	$otherLanguagePage = $otherLanguageNodeTranslation->getPublicNodeVersion()->getRef($em);
    	$myLanguagePage = $otherLanguagePage->deepClone($em);
    	$node = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $locale, $node, $user);
    	return $this->redirect($this->generateUrl("KunstmaanAdminBundle_pages_edit", array('id'=>$id)));
    }

    /**
     * @Route("/{id}/createemptypage", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminBundle_pages_createemptypage")
     * @Template()
     */
    public function createEmptyPageAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$request = $this->getRequest();
    	$locale = $request->getSession()->getLocale();
    	$node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
    	$entityname = $node->getRefEntityname();
    	$myLanguagePage = new $entityname();
    	$myLanguagePage->setTitle("New page");
    	$em->persist($myLanguagePage);
    	$em->flush();
    	$node = $em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->createNodeTranslationFor($myLanguagePage, $locale, $node, $user);
    	return $this->redirect($this->generateUrl("KunstmaanAdminBundle_pages_edit", array('id'=>$id)));
    }

	/**
     * @Route("/{id}/publish", requirements={"_method" = "GET|POST", "id" = "\d+"}, name="KunstmaanAdminBundle_pages_edit_publish")
     * @Template()
     */
    public function publishAction($id)
    {
    	$em = $this->getDoctrine()->getEntityManager();
    	$request = $this->getRequest();
    	$locale = $request->getSession()->getLocale();
    	$node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
    	$nodeTranslation = $node->getNodeTranslation($locale);
    	$nodeTranslation->setOnline(true);
    	$em->persist($nodeTranslation);
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
    	$request = $this->getRequest();
    	$locale = $request->getSession()->getLocale();
    	$node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
    	$nodeTranslation = $node->getNodeTranslation($locale);
    	$nodeTranslation->setOnline(false);
    	$em->persist($nodeTranslation);
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
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$request = $this->getRequest();
    	$locale = $request->getSession()->getLocale();
    	$saveasdraft = $request->get("saveasdraft");
    	$saveandpublish = $request->get("saveandpublish");
    	$draft = ($subaction == "draft");

        $node = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($id);
        $nodeTranslation = $node->getNodeTranslation($locale);
        if(!$nodeTranslation){
        	return $this->render('KunstmaanAdminBundle:Pages:pagenottranslated.html.twig', array('node' => $node, 'nodeTranslations' => $node->getNodeTranslations(), 'nodemenu' => new NodeMenu($this->container, $locale, $node, 'write')));
        }
        $nodeVersions = $nodeTranslation->getNodeVersions();
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();
        $draftNodeVersion = $nodeTranslation->getNodeVersion('draft');
        $page = $em->getRepository($nodeVersion->getRefEntityname())->find($nodeVersion->getRefId());
        if(!is_null($this->getRequest()->get('version'))) {
        	$repo->revert($page, $this->getRequest()->get('version'));
        }
        if($draft){
        	$nodeVersion = $nodeTranslation->getNodeVersion('draft');
        	$page = $nodeVersion->getRef($em);
        } else {
        	if(is_string($saveasdraft) && $saveasdraft != ''){
        		$page = $page->deepClone($em);
        		$nodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->createNodeVersionFor($page, $nodeTranslation, $user, 'draft');
        		$draft = true;
        		$subaction = "draft";
        	}
        }

        $addpage = $request->get("addpage");
        $addpagetitle = $request->get("addpagetitle");
        if(is_string($addpage) && $addpage != ''){
        	$newpage = new $addpage();
        	if(is_string($addpagetitle) && $addpagetitle != ''){
        		$newpage->setTitle($addpagetitle);
        	} else {
        		$newpage->setTitle('New page');
        	}
        	$em->persist($newpage);
        	$em->flush();

        	$nodeparent = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($page);
        	$newpage->setParent($page);
            $nodenewpage = $em->getRepository('KunstmaanAdminNodeBundle:Node')->createNodeFor($newpage, $locale, $user);

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

        $topnodes   = $em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($user, 'write');

        $formfactory = $this->container->get('form.factory');
        $formbuilder = $this->createFormBuilder();

        //add the specific data from the custom page
        $formbuilder->add('main', $page->getDefaultAdminType());
        $formbuilder->add('node', $node->getDefaultAdminType($this->container));

        $formbuilder->setData(array('node' => $node, 'main' => $page));

        //handle the pagepart functions (fetching, change form to reflect all fields, assigning data, etc...)
        $pagepartadmins = array();
        foreach($page->getPagePartAdminConfigurations() as $pagePartAdminConfiguration){
        	$pagepartadmin = $this->get("pagepartadmin.factory")->createList($pagePartAdminConfiguration, $em, $page, null, $this->container);
        	$pagepartadmin->preBindRequest($request);
        	$pagepartadmin->adaptForm($formbuilder, $formfactory);
        	$pagepartadmins[] = $pagepartadmin;
        }

        if ($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')) {
            $permissionadmin = $this->get("kunstmaan_admin.permissionadmin");
            $permissionadmin->initialize($node, $em, $page->getPossiblePermissions());
        }

        $seoform = $this->createForm(new SEOType(), $nodeTranslation->getSEO());
        $form = $formbuilder->getForm();
        if ($request->getMethod() == 'POST') {
            $form           ->bindRequest($request);
            $seoform        ->bindRequest($request);
            foreach($pagepartadmins as $pagepartadmin) {
            	$pagepartadmin  ->bindRequest($request);
            }

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
                	$newpublicpage = $page->deepClone($em);
                	$nodeVersion = $em->getRepository('KunstmaanAdminNodeBundle:NodeVersion')->createNodeVersionFor($newpublicpage, $nodeTranslation, $user, 'public');
                	$nodeTranslation->setPublicNodeVersion($nodeVersion);
                	$nodeTranslation->setTitle($newpublicpage->__toString());
            		$nodeTranslation->setSlug(Slugifier::slugify($newpublicpage->__toString()));
            		$nodeTranslation->setOnline($newpublicpage->isOnline());
                	$em->persist($nodeTranslation);
                	$em->flush();
                	$draft = false;
                	$subaction = "public";
                }

                return $this->redirect($this->generateUrl('KunstmaanAdminBundle_pages_edit', array(
                    'id' => $node->getId(),
                	'subaction' => $subaction
                )));
            }
        }

        $nodeMenu = new NodeMenu($this->container, $locale, $node, 'write');
        
        $viewVariables = array(
            'topnodes'          => $topnodes,
            'page'              => $page,
            'entityname'        => ClassLookup::getClass($page),
            'form'              => $form->createView(),
        	'seoform'			=> $seoform->createView(),
            'pagepartadmins'    => $pagepartadmins,
            'nodeVersions'      => $nodeVersions,
            'nodemenu'          => $nodeMenu,
            'node'              => $node,
        	'nodeTranslation'   => $nodeTranslation,
        	'draft'             => $draft,
        	'draftNodeVersion'  => $draftNodeVersion,
        	'subaction'         => $subaction
        );
        if($this->get('security.context')->isGranted('ROLE_PERMISSIONMANAGER')){
            $viewVariables['permissionadmin'] = $permissionadmin;
        }
        return $viewVariables;
    }
    
    /**
     * @Route("/movenodes", name="KunstmaanAdminBundle_pages_movenodes")
     * @Method({"GET", "POST"})
     */
    public function movenodesAction(){
    	$request = $this->getRequest();
    	$em = $this->getDoctrine()->getEntityManager();
    
    	$parentid = $request->get('parentid');
    	$parent = $em->getRepository('KunstmaanAdminNodeBundle:Node')->find($parentid);
    
    	$fromposition = $request->get('fromposition');
    	$afterposition = $request->get('afterposition');
    	
    	foreach($parent->getChildren() as $child){
    		if($child->getSequencenumber() == $fromposition){
    			if($child->getSequencenumber() > $afterposition){
    				$child->setSequencenumber($afterposition + 1);
    				$em->persist($child);
    			}else{
    				$child->setSequencenumber($afterposition);
    				$em->persist($child);
    			}
    		}else{
    			if($child->getSequencenumber() > $fromposition && $child->getSequencenumber() <= $afterposition){
    				$newpos = $child->getSequencenumber()-1;
    				$child->setSequencenumber($newpos);
    				$em->persist($child);
    			}else{
    				if($child->getSequencenumber() < $fromposition && $child->getSequencenumber() > $afterposition){
    					$newpos = $child->getSequencenumber()+1;
    					$child->setSequencenumber($newpos);
    					$em->persist($child);
    				}
    			}
    		}
    
    		$em->flush();
    	}
    	return array("success" => true);
    }

}
