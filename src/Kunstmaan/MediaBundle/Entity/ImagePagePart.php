<?php

namespace  Kunstmaan\MediaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Form\ImagePagePartAdminType;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;

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

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $link;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $alttext;

    /**
     * @ORM\OneToOne(targetEntity="Media")
     */
    public $media;


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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set link
     *
     * @param string $link
     */
    public function setLink($link) {
        $this->link = $link;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink() {
        return $this->link;
    }

    /**
     * Set alt text
     *
     * @param string $alttext
     */
    public function setAlttext($alttext) {
        $this->alttext = $alttext;
    }

    /**
     * Get media
     *
     * @return Kunstmaan\MediaBundle\Entity\Media
     */
    public function getMedia() {
        return $this->media;
    }

    /**
     * Set media
     *
     * @param Kunstmaan\MediaBundle\Entity\Media $media
     */
    public function setMedia($media) {
        $this->media = $media;
    }

    /**
     * Get alt text
     *
     * @return string
     */
    public function getAlttext() {
        return $this->alttext;
    }

    public function __toString() {
        return $this->getMedia()->getUrl();
    }

    public function getDefaultView(){
        return "KunstmaanMediaBundle:ImagePagePart:view.html.twig";
    }

    public function getDefaultAdminType(){
        return new ImagePagePartAdminType();
    }
}