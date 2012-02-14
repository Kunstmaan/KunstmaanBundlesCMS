<?php

namespace Kunstmaan\SearchBundle\Entity;

use Kunstmaan\ViewBundle\Entity\SearchPage;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\SearchBundle\Repository\SearchedForRepository")
 * @ORM\Table(name="searchedfor")
 * @ORM\HasLifecycleCallbacks()
 */
class SearchedFor{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $query;
    
    /**
     * @ORM\Column(type="datetime")
     */
    protected $createdat;
    
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\ViewBundle\Entity\SearchPage")
     * @ORM\JoinColumn(name="searchpage", referencedColumnName="id")
     */
    protected $searchpage;

    public function __construct($query, SearchPage $searchpage) {
    	$this->setQuery($query);
    	$this->setSearchpage($searchpage);
    	$this->setCreatedAt(new \DateTime());
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
    
    public function getQuery(){
    	return $this->query;
    }
    
    public function setQuery($query){
    	$this->query = $query;
    }
    
    public function getSearchpage(){
    	return $this->searchpage;
    }
    
    public function setSearchpage($searchpage){
    	$this->searchpage = $searchpage;
    }
    
    public function setCreatedAt($created){
    	$this->createdat = $created;
    }
    
    public function getCreatedAt(){
    	return $this->createdat;
    }
}
