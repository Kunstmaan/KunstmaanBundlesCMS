<?php

namespace Kunstmaan\MediaPagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Form\SlidePagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_slide_page_parts")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_slide_page_parts')]
class SlidePagePart extends AbstractPagePart
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
     * @return SlidePagePart
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
        return '@KunstmaanMediaPagePart/SlidePagePart/view.html.twig';
    }

    /**
     * @return string
     */
    public function getAdminView()
    {
        return '@KunstmaanMediaPagePart/SlidePagePart/admin-view.html.twig';
    }

    public function getDefaultAdminType()
    {
        return SlidePagePartAdminType::class;
    }
}
