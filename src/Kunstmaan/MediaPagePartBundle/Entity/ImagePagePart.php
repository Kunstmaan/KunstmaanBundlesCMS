<?php

namespace Kunstmaan\MediaPagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Kunstmaan\MediaPagePartBundle\Form\ImagePagePartAdminType;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_image_page_parts")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_image_page_parts')]
class ImagePagePart extends AbstractPagePart
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    #[ORM\Column(name: 'link', type: 'string', nullable: true)]
    protected $link;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="open_in_new_window")
     */
    #[ORM\Column(name: 'open_in_new_window', type: 'boolean', nullable: true)]
    protected $openInNewWindow;

    /**
     * @ORM\Column(type="string", nullable=true, name="alt_text")
     */
    #[ORM\Column(name: 'alt_text', type: 'string', nullable: true)]
    protected $altText;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'media_id', referencedColumnName: 'id')]
    protected $media;

    /**
     * Get opennewwindow
     *
     * @return bool
     */
    public function getOpenInNewWindow()
    {
        return $this->openInNewWindow;
    }

    /**
     * Set openwinnewwindow
     *
     * @param bool $openInNewWindow
     *
     * @return ImagePagePart
     */
    public function setOpenInNewWindow($openInNewWindow)
    {
        $this->openInNewWindow = $openInNewWindow;

        return $this;
    }

    /**
     * Set link
     *
     * @param string $link
     *
     * @return ImagePagePart
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * Set alt text
     *
     * @param string $altText
     *
     * @return ImagePagePart
     */
    public function setAltText($altText)
    {
        $this->altText = $altText;

        return $this;
    }

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
     * @return ImagePagePart
     */
    public function setMedia($media)
    {
        $this->media = $media;

        return $this;
    }

    /**
     * Get alt text
     *
     * @return string
     */
    public function getAltText()
    {
        return $this->altText;
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
        return '@KunstmaanMediaPagePart/ImagePagePart/view.html.twig';
    }

    /**
     * @return string
     */
    public function getAdminView()
    {
        return '@KunstmaanMediaPagePart/ImagePagePart/admin-view.html.twig';
    }

    public function getDefaultAdminType()
    {
        return ImagePagePartAdminType::class;
    }
}
