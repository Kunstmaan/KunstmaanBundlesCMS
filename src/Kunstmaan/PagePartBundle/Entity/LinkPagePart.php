<?php

namespace  Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType;

/**
 * @ORM\Entity
 * @ORM\Table(name="linkpagepart")
 */
class LinkPagePart {

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string", nullable="true")
     */
    protected $url;
    
    public function __construct() {
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id){
        $this->id = $id;
    }

    /**
     * Get pageId
     *
     * @return integer
     */
    public function getId(){
        return $this->id;
    }

    public function setUrl($url){
    	$this->url = $url;
    }

    public function getUrl(){
    	return $this->url;
    }
    
    
    public function __toString(){
        return "LinkPagePart";
    }

    public function getDefaultView(){
        return "KunstmaanPagePartBundle:LinkPagePart:view.html.twig";
    }

    public function getDefaultAdminType(){
        return new LinkPagePartAdminType();
    }
}