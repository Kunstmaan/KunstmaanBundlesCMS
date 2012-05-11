<?php

namespace  Kunstmaan\MediaPagePartBundle\Entity;

use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaPagePartBundle\Form\DownloadPagePartAdminType;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;

/**
 * DownloadPagePart
 * 
 * @ORM\Entity
 * @ORM\Table(name="pagepart_download")
 */
class DownloadPagePart extends AbstractPagePart
{

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     */
    public $media;

    /**
     * Get media
     *
     * @return Kunstmaan\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set media
     *
     * @param Kunstmaan\MediaBundle\Entity\Media $media
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        if ($this->getMedia()) {
            return $this->getMedia()->getUrl();
        }

        return "";
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "KunstmaanMediaPagePartBundle:DownloadPagePart:view.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getElasticaView()
    {
    	return "KunstmaanMediaPagePartBundle:DownloadPagePart:elastica.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new DownloadPagePartAdminType();
    }
}