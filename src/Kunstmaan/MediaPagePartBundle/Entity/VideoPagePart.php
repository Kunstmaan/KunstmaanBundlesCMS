<?php

namespace Kunstmaan\MediaPagePartBundle\Entity;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaPagePartBundle\Form\VideoPagePartAdminType;
use Assetic\AssetManager;
use Assetic\Asset\AssetCollection;
use Assetic\Asset\FileAsset;

/**
 * VideoPagePart
 * 
 * @ORM\Entity
 * @ORM\Table(name="pagepart_video")
 */
class VideoPagePart extends AbstractPagePart
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
        return "KunstmaanMediaPagePartBundle:VideoPagePart:view.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new VideoPagePartAdminType();
    }
}
