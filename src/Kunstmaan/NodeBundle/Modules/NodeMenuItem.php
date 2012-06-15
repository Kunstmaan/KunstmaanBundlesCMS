<?php

namespace Kunstmaan\AdminNodeBundle\Modules;

use Kunstmaan\AdminNodeBundle\Entity\Node;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;
use Symfony\Component\Translation\Translator;
use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class NodeMenuItem
{
    private $em;
    private $node;
    private $nodeTranslation;
    private $lang;
    private $lazyChildren = null;
    private $parent;
    private $menu;

    /**
     * @param FactoryInterface $factory
     */
    public function __construct($em, Node $node, NodeTranslation $nodeTranslation, $lang, $parent, $menu)
    {
        $this->em = $em;
        $this->node = $node;
        $this->nodeTranslation = $nodeTranslation;
        $this->lang = $lang;
        $this->parent = $parent;
        $this->menu = $menu;
    }

    public function getId(){
    	return $this->node->getId();
    }

    public function getNode(){
    	return $this->node;
    }

    public function getNodeTranslation(){
    	return $this->nodeTranslation;
    }

    public function getLang(){
    	return $this->lang;
    }

    public function getTitle(){
    	$nodeTranslation = $this->getNodeTranslation();
    	if($nodeTranslation){
    		return $nodeTranslation->getTitle();
    	}
    	return "Untranslated";
    }

    public function getOnline(){
    	$nodeTranslation = $this->getNodeTranslation();
    	if($nodeTranslation){
    		return $nodeTranslation->isOnline();
    	}
    	return false;
    }

    public function getSlugPart(){
    	$nodeTranslation = $this->getNodeTranslation();
    	if($nodeTranslation){
    		return $nodeTranslation->getFullSlug();
    	}
    	return null;
    }

    public function getSlug(){
    	$result = $this->getSlugPart();
    	return $result;
    }

    public function getParent(){
    	return $this->parent;
    }
    
    public function getParents()
    {
        $parent = $this->getParent();
        $parents=array();
        while($parent!=null){
            $parents[] = $parent;
            $parent = $parent->getParent();
        }
        return array_reverse($parents);
    }

    public function getChildren($includeoffline = FALSE, $includehiddenfromnav = TRUE){
    	if(is_null($this->lazyChildren)){
    		$this->lazyChildren = array();
    		//$children = $this->node->getChildren();
    		$NodeRepo = $this->em->getRepository('KunstmaanAdminNodeBundle:Node');
    		$children = $NodeRepo->getChildNodes($this->node->getId(), $this->lang, $this->menu->getUser(), $this->menu->getPermission()); 
    		foreach($children as $child){
    			$nodeTranslation = $child->getNodeTranslation($this->lang, $this->menu->isIncludeOffline());
    			if(!is_null($nodeTranslation)){
    				$this->lazyChildren[] = new NodeMenuItem($this->em, $child, $nodeTranslation, $this->lang, $this, $this->menu);
    			}
    		}
    	}
    	return array_filter($this->lazyChildren, function ($entry) use ($includeoffline, $includehiddenfromnav)
        {
            if(!$entry->getOnline() && !$includeoffline) {
                return false;
            }
            if($entry->getNode()->isHiddenFromNav() && !$includehiddenfromnav) {
                return false;
            }
            return true;
        });
    }

    public function getPage(){
    	return $this->getNodeTranslation()->getRef($this->em);
    }

    public function getActive(){
    	//TODO: change to something like in_array() but that didn't work
    	$bc = $this->menu->getBreadCrumb();
    	foreach($bc as $bcItem){
    		if($bcItem->getSlug() == $this->getSlug()){
    			return true;
    		}
    	}
    	return false;
    }
}
