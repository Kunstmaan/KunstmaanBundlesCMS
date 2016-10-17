<?php

namespace  Kunstmaan\MediaPagePartBundle\Entity;

use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaPagePartBundle\Form\DownloadPagePartAdminType;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * DownloadPagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_download_page_parts")
 */
class DownloadPagePart extends AbstractPagePart
{

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    protected $media;

    /**
     * Get media
     *
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    /**
     * Set media
     *
     * @param Media $media
     *
     * @return DownloadPagePart
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getMedia()) {
            return $this->getMedia()->getUrl();
        }

        return "";
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanMediaPagePartBundle:DownloadPagePart:view.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return DownloadPagePartAdminType::class;
    }
}
