<?php

namespace  Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Form\ImagePagePartAdminType;

/**
 * @ORM\Entity
 * @ORM\Table(name="imagepagepart")
 */
class ImagePagePart {

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

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
        return "ImagePagePart ".$this->getTitle();
    }

    public function getDefaultView(){
        return "KunstmaanMediaBundle:ImagePagePart:view.html.twig";
    }

    public function getDefaultAdminType(){
        return new ImagePagePartAdminType();
    }
}