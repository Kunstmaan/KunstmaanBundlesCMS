<?php

namespace  Kunstmaan\PagePartBundle\Entity;

use Kunstmaan\PagePartBundle\Helper\IsPagePart;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

/**
 * Class that defines a header page part object to add to a page
 *
 * @author Kristof Van Cauwenbergh
 *
 * @ORM\Entity
 * @ORM\Table(name="headerpagepart")
 */
class HeaderPagePart implements IsPagePart{

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $niv;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

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

    /**
     * Set niv
     *
     * @param int $niv
     */
    public function setNiv($niv) {
        $this->niv = $niv;
    }

    /**
     * Get niv
     *
     * @return int
     */
    public function getNiv() {
        return $this->niv;
    }

    /**
     * Set content
     *
     * @param string $content
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    public function __toString(){
        return "HeaderPagePart ".$this->getTitle();
    }

    public function getDefaultView(){
        return "KunstmaanPagePartBundle:HeaderPagePart:view.html.twig";
    }
    
    public function getElasticaView(){
    	return $this->getDefaultView();
    }

    public function getDefaultAdminType(){
        return new HeaderPagePartAdminType();
    }
}