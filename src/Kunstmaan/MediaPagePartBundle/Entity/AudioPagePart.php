<?php

namespace Kunstmaan\MediaPagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Form\AudioPagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_audio_page_parts")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_audio_page_parts')]
class AudioPagePart extends AbstractPagePart
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
     * @return AudioPagePart
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
        return '@KunstmaanMediaPagePart/AudioPagePart/view.html.twig';
    }

    /**
     * @return string
     */
    public function getAdminView()
    {
        return '@KunstmaanMediaPagePart/AudioPagePart/admin-view.html.twig';
    }

    public function getDefaultAdminType()
    {
        return AudioPagePartAdminType::class;
    }
}
