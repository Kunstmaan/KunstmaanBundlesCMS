<?php

namespace Kunstmaan\MediaPagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Form\VideoPagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_video_page_parts")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_video_page_parts')]
class VideoPagePart extends AbstractPagePart
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'media_id', referencedColumnName: 'id')]
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
     * @return VideoPagePart
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
            return $this->getMedia()->getUrl() ?? '';
        }

        return '';
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '@KunstmaanMediaPagePart/VideoPagePart/view.html.twig';
    }

    /**
     * @return string
     */
    public function getAdminView()
    {
        return '@KunstmaanMediaPagePart/VideoPagePart/admin-view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return VideoPagePartAdminType::class;
    }
}
