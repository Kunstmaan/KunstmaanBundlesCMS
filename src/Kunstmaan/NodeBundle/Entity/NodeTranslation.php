<?php
// src/Blogger/BlogBundle/Entity/Blog.php

namespace Kunstmaan\AdminNodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Kunstmaan\SearchBundle\Entity\Indexable;
use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\AdminNodeBundle\Form\NodeAdminType;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminNodeBundle\Repository\NodeTranslationRepository")
 * @ORM\Table(name="nodetranslation")
 */
class NodeTranslation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Node")
     * @ORM\JoinColumn(name="node", referencedColumnName="id")
     */
    protected $node;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $lang;
    
    /**
     * @ORM\Column(type="boolean")
     */
    protected $online;
    
    /**
     * @ORM\Column(type="string")
     */
    protected $title;
    
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $slug;
    
    /**
     * @ORM\ManyToOne(targetEntity="NodeVersion")
     * @ORM\JoinColumn(name="publicNodeVersion", referencedColumnName="id")
     */
    protected $publicNodeVersion;
    
    /**
     * @ORM\OneToMany(targetEntity="NodeVersion", mappedBy="nodeTranslation")
     * @ORM\OrderBy({"version" = "DESC"})
     */
    protected $nodeVersions;

    public function __construct() {
    	$this->nodeVersions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set node
     *
     * @param integer $node
     */
    public function setNode($node)
    {
    	$this->node = $node;
    }
    
    /**
     * Get Node
     *
     * @return integer
     */
    public function getNode() {
    	return $this->node;
    }
    
    /**
     * Set lang
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
    	$this->lang = $lang;
    }
    
    /**
     * Get lang
     *
     * @return string
     */
    public function getLang()
    {
    	return $this->lang;
    }

    /**
     * Is online
     *
     * @return boolean
     */
    public function isOnline() {
        return $this->online;
    }

    /**
     * Set online
     *
     * @param boolean $online
     */
    public function setOnline($online) {
        $this->online = $online;
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
     * Set slug
     *
     * @param string $slug
     */
    public function setSlug($slug) {
    	$this->slug = $slug;
    }

    /**
     * Get slug
     *
     * @return string
     */
 	public function getSlug() {
    	$node = $this->getNode();
    	$slug = "";
    	if($node->getParent()!=null) $slug = $slug.$this->getParentSlug($node);
    	$slug = $slug . $this->slug;
        return $slug;
    }
    
    public function getParentSlug($node) {
    	return $node->getParent()->getNodeTranslation($this->lang)->getSlug() . "/";
    }
    

    /**
     * Set publicNodeVersion
     *
     */
    public function setPublicNodeVersion($publicNodeVersion)
    {
    	$this->publicNodeVersion = $publicNodeVersion;
    }
    
    /**
     * Get publicNodeVersion
     *
     */
    public function getPublicNodeVersion() {
    	return $this->publicNodeVersion;
    }
    
	public function getNodeVersions() {
    	return $this->nodeVersions;
    }
    
    public function setNodeVersions($nodeVersions) {
    	$this->nodeVersions = $nodeVersions;
    }
    
    public function getNodeVersion($type){
    	$nodeVersions = $this->getNodeVersions();
    	foreach($nodeVersions as $nodeVersion){
    		if($type == $nodeVersion->getType()){
    			return $nodeVersion;
    		}
    	}
    	return null;
    }
    
    /**
     * Add nodeVersion
     *
     * @param NodeVersion $nodeVersion
     */
    public function addNodeVersion(NodeVersion $nodeVersion) {
    	$this->nodeVersions[] = $nodeVersion;
    	$nodeVersion->setNodeTranslation($this);
    }
    
    public function disableNodeVersionsLazyLoading() {
    	if (is_object($this->nodeVersions)) {
    		$this->nodeVersions->setInitialized(true);
    	}
    }

    public function getDefaultAdminType($container){
        return new NodeAdminType($container);
    }
    
    public function getRef($em, $type = "public") {
    	$nodeVersion = $this->getNodeVersion($type);
    	if($nodeVersion) {
    		return $em->getRepository($nodeVersion->getRefEntityname())->find($nodeVersion->getRefId());
    	}
    	return null;
    }
    
    public function getSearchContentForNode($container, $entity, $field){
    	$page = $entity->getRef($container->get('doctrine')->getEntityManager());
    	if($page instanceof Indexable) {
        	return $page;
        }

        return null;
    }

    /**
     * Returns the date the first nodeversion was created
     *
     * @return mixed
     */
    public function getCreated() {
        $versions = $this->getNodeVersions();
        $firstVersion = $versions->first();

        return $firstVersion->getCreated();
    }

    /**
     * Returns the date the last nodeversion was updated
     * 
     * @return mixed
     */
    public function getUpdated() {
        $versions = $this->getNodeVersions();
        $lastVersion = $versions->last();

        return $lastVersion->getUpdated();
    }
}