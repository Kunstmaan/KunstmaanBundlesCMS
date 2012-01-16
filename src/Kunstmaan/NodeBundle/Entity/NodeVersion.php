<?php
// src/Blogger/BlogBundle/Entity/Blog.php

namespace Kunstmaan\AdminNodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\AdminNodeBundle\Form\NodeAdminType;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminNodeBundle\Repository\NodeVersionRepository")
 * @ORM\Table(name="nodeversion")
 * @ORM\HasLifecycleCallbacks()
 */
class NodeVersion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="NodeTranslation")
     * @ORM\JoinColumn(name="nodetranslation", referencedColumnName="id")
     */
    protected $nodeTranslation;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $type;
    
    /**
     * @ORM\Column(type="bigint")
     */
    protected $version;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $owner;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $refId;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $refEntityname;

    public function __construct() {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
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
    
    /**
     * Set nodeTranslation
     *
     * @param integer $nodeTranslation
     */
    public function setNodeTranslation($nodeTranslation) {
    	$this->nodeTranslation = $nodeTranslation;
    }
    
    /**
     * Get NodeTranslation
     *
     * @return integer
     */
    public function getNodeTranslation() {
    	return $this->nodeTranslation;
    }
    
    /**
     * Get type
     *
     * @return string
     */
    public function getType() {
    	return $this->type;
    }
    
    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type) {
    	$this->type = $type;
    }
    
    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion() {
    	return $this->version;
    }
    
    /**
     * Set version
     *
     * @param string $version
     */
    public function setVersion($num) {
    	$this->version = $num;
    }
    
    /**
     * Set owner
     *
     * @param string $owner
     */
    public function setOwner($owner) {
    	$this->owner = $owner;
    }
    
    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner() {
    	return $this->owner;
    }
    
    /**
     * Set created
     *
     * @param datetime $created
     */
    public function setCreated($created) {
    	$this->created = $created;
    }
    
    /**
     * Get created
     *
     * @return datetime
     */
    public function getCreated() {
    	return $this->created;
    }
    
    /**
     * Set updated
     *
     * @param datetime $updated
     */
    public function setUpdated($updated) {
    	$this->updated = $updated;
    }
    
    /**
     * Get updated
     *
     * @return datetime
     */
    public function getUpdated() {
    	return $this->updated;
    }
    
    /**
     * @ORM\preUpdate
     */
    public function setUpdatedValue() {
    	$this->setUpdated(new \DateTime());
    }

    /**
     * Get refId
     *
     * @return integer
     */
    public function getRefId() {
        return $this->refId;
    }

    /**
     * Set refId
     *
     * @param string $refId
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
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

    public function getDefaultAdminType($container){
        return new NodeAdminType($container);
    }
    
    public function getRef($em) {
    	return $em->getRepository($this->getRefEntityname())->find($this->getRefId());
    }
}