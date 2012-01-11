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
     */
    protected $children;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $refId;

    /**
     * @ORM\Column(type="string")
     */
    protected $refEntityname;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $slug;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $online;


    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($num)
    {
        $this->id = $num;
    }

    /**
     * Get refId
     *
     * @return integer
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * Set refId
     *
     * @param string $refId
     */
    public function setRefId($num)
    {
        $this->refId = $num;
    }

    /**
     * Set refEntityname
     *
     * @param string $refEntityname
     */
    public function setRefEntityname($refEntityname)
    {
        $this->refEntityname = $refEntityname;
    }

    /**
     * Get refEntityname
     *
     * @return string 
     */
    public function getRefEntityname()
    {
        return $this->refEntityname;
    }

    /**
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get created
     *
     * @return datetime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param datetime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * Get updated
     *
     * @return datetime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    


    /**
     * @ORM\preUpdate
     */
    public function setUpdatedValue()
    {
       $this->setUpdated(new \DateTime());
    }

    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Add children
     *
     * @param \Kunstmaan\AdminBundle\Entity\Page $children
     */
    public function addChild(Page $child)
    {
        $this->children[] = $child;

        $child->setParent($this);
    }


    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function disableChildrenLazyLoading()
    {
        if (is_object($this->children)) {
            $this->children->setInitialized(true);
        }
    }

    /**
     * Set parent
     *
     * @param integer $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get parent
     *
     * @return integer 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set sequencenumber
     *
     * @param integer $sequencenumber
     */
    public function setSequencenumber($sequencenumber)
    {
        $this->sequencenumber = $sequencenumber;
    }

    /**
     * Get sequencenumber
     *
     * @return integer 
     */
    public function getSequencenumber()
    {
        return $this->sequencenumber;
    }

    /**
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Is online
     *
     * @return boolean
     */
    public function isOnline()
    {
        return $this->online;
    }

    /**
     * Set online
     *
     * @param boolean $online
     */
    public function setOnline($online)
    {
        $this->online = $online;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Add children
     *
     * @param Kunstmaan\AdminBundle\Entity\Page $children
     */
    public function addNode(\Kunstmaan\AdminBundle\Entity\Node $children)
    {
        $this->children[] = $children;
    }

    public function getDefaultAdminType(){
        return new NodeAdminType();
    }
    
    public function getRef($em){
    	return $em->getRepository($this->getRefEntityname())->find($this->getRefId());
    }
}