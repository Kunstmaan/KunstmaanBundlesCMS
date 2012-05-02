<?php

namespace  Kunstmaan\PagePartBundle\Entity;

use Kunstmaan\PagePartBundle\Helper\IsPagePart;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType;

/**
 * @ORM\Entity
 * @ORM\Table(name="linkpagepart")
 */
class LinkPagePart implements IsPagePart{

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

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $openinnewwindow;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $text;

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

    /**
     * Get opennewwindow
     *
     * @return bool
     */
    public function getOpenInNewWindow() {
    	return $this->openinnewwindow;
    }

    /**
     * Set openwinnewwindow
     *
     * @param bool $link
     */
    public function setOpenInNewWindow($link) {
    	$this->openinnewwindow = $link;
    }

    /**
     * Set text
     *
     * @param string $text
     */
    public function setText($text) {
    	$this->text = $text;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText() {
    	return $this->text;
    }

    public function __toString(){
        return "LinkPagePart";
    }

    public function getDefaultView(){
        return "KunstmaanPagePartBundle:LinkPagePart:view.html.twig";
    }
    
    public function getElasticaView(){
    	return $this->getDefaultView();
    }

    public function getDefaultAdminType(){
        return new LinkPagePartAdminType();
    }
}