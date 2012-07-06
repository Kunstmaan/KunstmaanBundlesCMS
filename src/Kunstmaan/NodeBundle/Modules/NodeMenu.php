<?php

namespace Kunstmaan\AdminNodeBundle\Modules;

use Symfony\Component\DependencyInjection\ContainerInterface;

use Kunstmaan\AdminNodeBundle\Entity\HasNodeInterface;
use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Symfony\Component\Translation\Translator;
use Knp\Menu\FactoryInterface;

/**
 * NodeMenu
 */
class NodeMenu
{
    private $em;
    private $lang;
    private $topNodeMenuItems = array();
    private $breadCrumb = array();
    private $container = null;
    private $includeoffline = false;
    private $permission = null;
    private $user = null;

    /**
     * @param ContainerInterface $container            The container
     * @param string             $lang                 The language
     * @param Node               $currentNode          The node
     * @param string             $permission           The permission
     * @param boolean            $includeoffline       Include offline pages
     * @param boolean            $includehiddenfromnav Include hidden pages
     */
    public function __construct($container, $lang, Node $currentNode = null, $permission = 'read', $includeoffline = false, $includehiddenfromnav = false)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
        $this->lang = $lang;
        $this->includeoffline = $includeoffline;
        $this->permission = $permission;
        $tempNode = $currentNode;

        //Breadcrumb
        $nodeBreadCrumb = array();
        while($tempNode){
        	array_unshift($nodeBreadCrumb, $tempNode);
        	$tempNode = $tempNode->getParent();
        }
        $parentNodeMenuItem = null;
        foreach($nodeBreadCrumb as $nodeBreadCrumbItem){
        	$nodeTranslation = $nodeBreadCrumbItem->getNodeTranslation($lang, $this->includeoffline);
        	if(!is_null($nodeTranslation)){
        		$nodeMenuItem = new NodeMenuItem($this->em, $nodeBreadCrumbItem, $nodeTranslation, $lang, $parentNodeMenuItem, $this);
        		$this->breadCrumb[] = $nodeMenuItem;
        		$parentNodeMenuItem = $nodeMenuItem;
        	}
        }

        $permissionManager = $container->get('kunstmaan_admin.permissionmanager');
        $this->user = $this->container->get('security.context')->getToken()->getUser();

        $this->user = $permissionManager->getCurrentUser($this->user, $this->em);

        //topNodes
        $topNodes = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->getTopNodes($this->lang, $this->user, $permission, $includehiddenfromnav);
        foreach($topNodes as $topNode){
        	$nodeTranslation = $topNode->getNodeTranslation($lang, $this->includeoffline);
        	if(!is_null($nodeTranslation)){
	        	if(sizeof($this->breadCrumb)>0 && $this->breadCrumb[0]->getNode()->getId() == $topNode->getId()){
	        		$this->topNodeMenuItems[] = $this->breadCrumb[0];
	        	} else {
	        		$this->topNodeMenuItems[] = new NodeMenuItem($this->em, $topNode, $nodeTranslation, $lang, null, $this);
	        	}
        	}
        }
    }

    public function getTopNodes()
    {
        return $this->topNodeMenuItems;
    }

    public function getCurrent()
    {
    	if(sizeof($this->breadCrumb)>0){
    		return $this->breadCrumb[sizeof($this->breadCrumb)-1];
    	}
    	return null;
    }

    public function getActiveForDepth($depth)
    {
    	if(sizeof($this->breadCrumb)>=$depth){
    		return $this->breadCrumb[$depth-1];
    	}
    	return null;
    }

    public function getBreadCrumb()
    {
    	return $this->breadCrumb;
    }

    public function getNodeBySlug(NodeTranslation $parentNode, $slug)
    {
    	return $this->em->getRepository('KunstmaanAdminNodeBundle:NodeTranslation')->getNodeTranslationForSlug($parentNode, $slug);
    }

    public function getNodeByInternalName($internalName, $parent = null)
    {
    	$node = null;

    	if(!is_null($parent)){
    		if($parent instanceof NodeTranslation){
    			$parent = $parent->getNode();
    		} else if ($parent instanceof NodeMenuItem){
    			$parent = $parent->getNode();
    		} else if ($parent instanceof HasNodeInterface){
    			$parent = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->getNodeFor($parent);
    		}
    		$node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->findOneBy(array('internalName' => $internalName, 'parent' => $parent->getId()));
    		if(is_null($node)){
    		    $nodes = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->findBy(array('internalName' => $internalName));
    		    foreach($nodes as $n){
    		        $p = $n;
    		        while(is_null($node) && !is_null($p->getParent())){
                        $pParent = $p->getParent();
    		            if($pParent->getId() == $parent->getId()){
    		                $node = $n;
    		                break;
    		            }
    		            $p = $pParent;
    		        }
    		    }
    		}
    	} else {
    		$node = $this->em->getRepository('KunstmaanAdminNodeBundle:Node')->findOneBy(array('internalName' => $internalName));
    	}
    	if(!is_null($node)){
    		$nodeTranslation = $node->getNodeTranslation($this->lang, $this->includeoffline);
    		if(!is_null($nodeTranslation)) {
                return $this->getNodemenuForNodeTranslation($nodeTranslation);
    		}
    	}

    	return null;
    }

    private function getNodemenuForNodeTranslation(NodeTranslation $nodeTranslation)
    {
        if(!is_null($nodeTranslation)) {
            $tempNode = $nodeTranslation->getNode();
            //Breadcrumb
            $nodeBreadCrumb = array();
            $parentNodeMenuItem = null;
            while($tempNode && is_null($parentNodeMenuItem)){
                array_unshift($nodeBreadCrumb, $tempNode);
                $tempNode = $tempNode->getParent();
                if(!is_null($tempNode)){
                    $parentNodeMenuItem = $this->getBreadCrumbItemByNode($tempNode);
                }
            }
            $nodeMenuItem = null;
            foreach($nodeBreadCrumb as $nodeBreadCrumbItem){
                $breadCrumbItemFromMain = $this->getBreadCrumbItemByNode($nodeBreadCrumbItem);
                if(!is_null($breadCrumbItemFromMain)){
                    $parentNodeMenuItem = $breadCrumbItemFromMain;
                }
                $nodeTranslation = $nodeBreadCrumbItem->getNodeTranslation($this->lang, $this->includeoffline);
                if(!is_null($nodeTranslation)){
                    $nodeMenuItem = new NodeMenuItem($this->em, $nodeBreadCrumbItem, $nodeTranslation, $this->lang, $parentNodeMenuItem, $this);
                    $parentNodeMenuItem = $nodeMenuItem;
                }
            }
            //$resultNodeMenuItem = new NodeMenuItem($this->em, $node, $nodeTranslation, $this->lang, $parentNodeMenuItem, $this);
            return $nodeMenuItem;
        }
        return null;
    }

    private function getBreadCrumbItemByNode(Node $node){
    	foreach($this->breadCrumb as $breadCrumbItem){
    		if($breadCrumbItem->getNode()->getId() == $node->getId()){
    			return $breadCrumbItem;
    		}
    	}
    	return null;
    }

    public function isIncludeOffline(){
    	return $this->includeoffline;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function getUser()
    {
        return $this->user;
    }

}