<?php

namespace  Kunstmaan\MediaPagePartBundle\Entity;

use Kunstmaan\PagePartBundle\Entity\IsPagePart;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaPagePartBundle\Form\DownloadPagePartAdminType;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;

/**
 * @ORM\Entity
 * @ORM\Table(name="pagepart_download")
 */
class DownloadPagePart implements \Kunstmaan\PagePartBundle\Helper\IsPagePart{

    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
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

    public function __toString() {
        if($this->getMedia()) {
            return $this->getMedia()->getUrl();
        }
        return "";
    }

    public function getDefaultView(){
        return "KunstmaanMediaPagePartBundle:DownloadPagePart:view.html.twig";
    }
    
    public function getElasticaView(){
    	return "KunstmaanMediaPagePartBundle:DownloadPagePart:elastica.html.twig";
    }
    

    public function getDefaultAdminType(){
        return new DownloadPagePartAdminType();
    }
}