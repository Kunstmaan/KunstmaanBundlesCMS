<?php
// src/Blogger/BlogBundle/Entity/Blog.php

namespace Kunstmaan\AdminNodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\AdminNodeBundle\Form\NodeAdminType;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminNodeBundle\Repository\NodeRepository")
 * @ORM\Table(name="node")
 * @ORM\HasLifecycleCallbacks()
 */
class Node
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer")
     */
    protected $sequencenumber;

    /**
     * @ORM\OneToMany(targetEntity="Node", mappedBy="parent")
     * @ORM\OrderBy({"sequencenumber" = "ASC"})
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="NodeTranslation", mappedBy="node")
     */
    protected $nodeTranslations;

    /**
     * @ORM\Column(type="array", nullable=false)
     */
    protected $roles;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $deleted;

    /**
     * @ORM\Column(type="string")
     */
    protected $refEntityname;

    public function __construct() {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->nodeTranslations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deleted = false;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($num) {
        $this->id = $num;
    }

    public function getChildren() {
        return $this->children->filter( function($entry) {
		       if ($entry->isDeleted()) {
		           return false;
		       }
		       return true;
		    });
    }

    public function setChildren($children) {
        $this->children = $children;
    }

    /**
     * Add children
     *
     * @param \Kunstmaan\AdminNodeBundle\Entity\Node $child
     */
    public function addNode(Node $child) {
    	$this->children[] = $children;
    	$child->setParent($this);
    }

    public function disableChildrenLazyLoading() {
        if (is_object($this->children)) {
            $this->children->setInitialized(true);
        }
    }

    public function getNodeTranslations($includeoffline = false) {
    	return $this->nodeTranslations->filter( function($entry) use ($includeoffline) {
		       if ($includeoffline || $entry->isOnline()) {
		           return true;
		       }
		       return false;
		    });
    }

    public function setNodeTranslations($nodeTranslations) {
    	$this->nodeTranslations = $nodeTranslations;
    }

    public function getNodeTranslation($lang, $includeoffline = false){
    	$nodeTranslations = $this->getNodeTranslations($includeoffline);
    	foreach($nodeTranslations as $nodeTranslation){
    		if($lang == $nodeTranslation->getLang()){
    			return $nodeTranslation;
    		}
    	}
    	return null;
    }

    /**
     * Add nodeTranslation
     *
     * @param NodeTranslation $nodeTranslation
     */
    public function addNodeTranslation(NodeTranslation $nodeTranslation) {
    	$this->nodeTranslations[] = $nodeTranslation;
    	$nodeTranslation->setNode($this);
    }

    public function disableNodeTranslationsLazyLoading() {
    	if (is_object($this->nodeTranslations)) {
    		$this->nodeTranslations->setInitialized(true);
    	}
    }

    /**
     * Set parent
     *
     * @param integer $parent
     */
    public function setParent($parent) {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return integer
     */
    public function getParent() {
        return $this->parent;
    }

    /**
     * Set sequencenumber
     *
     * @param integer $sequencenumber
     */
    public function setSequencenumber($sequencenumber) {
        $this->sequencenumber = $sequencenumber;
    }

    /**
     * Get sequencenumber
     *
     * @return integer
     */
    public function getSequencenumber() {
        return $this->sequencenumber;
    }

    /**
     * Set the roles
     *
     * @param $roles
     */
    public function setRoles($roles) {
        $this->roles = $roles;
    }

    /**
     * Get the roles
     *
     * @return mixed
     */
    public function getRoles() {
        return $this->roles;
    }




    /**
     * Is online
     *
     * @return boolean
     */
    public function isDeleted() {
    	return $this->deleted;
    }

    /**
     * Set online
     *
     * @param boolean $online
     */
    public function setDeleted($deleted) {
    	$this->deleted = $deleted;
    }

    /**
     * Set refEntityname
     *
     * @param string $refEntityname
     */
    public function setRefEntityname($refEntityname) {
    	$this->refEntityname = $refEntityname;
    }

    /**
     * Get refEntityname
     *
     * @return string
     */
    public function getRefEntityname() {
    	return $this->refEntityname;
    }

    public function getDefaultAdminType($container) {
        return new NodeAdminType($container);
    }

    /**
     * @ORM\PrePersist
     */
    public function preInsert(){
    	if(!$this->sequencenumber){
    		$parent = $this->getParent();
    		if($parent){
    			$count = $parent->getChildren()->count();
    			$this->sequencenumber = $count+1;
    		}else $this->sequencenumber = 1;
    	}
    }
}