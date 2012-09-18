<?php

namespace Kunstmaan\MediaPagePartBundle\Entity;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaPagePartBundle\Form\ImagePagePartAdminType;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * ImagePagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_media_image_page_parts")
 */
class ImagePagePart extends AbstractPagePart
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $link;

    /**
     * @ORM\Column(type="boolean", nullable=true, name="open_in_new_window")
     */
    protected $openInNewWindow;

    /**
     * @ORM\Column(type="string", nullable=true, name="alt_text")
     */
    protected $altText;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     */
    public $media;

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
     * @param bool $link
     */
    public function setOpenInNewWindow($link)
    {
        $this->openInNewWindow = $link;
    }

    /**
     * Set link
     *
     * @param string $link
     */
    public function setLink($link)
    {
        $this->link = $link;
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
     */
    public function setAlttext($altText)
    {
        $this->altText = $altText;
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
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * Get alt text
     *
     * @return string
     */
    public function getAlttext()
    {
        return $this->altText;
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
        return "KunstmaanMediaPagePartBundle:ImagePagePart:view.html.twig";
    }

    /**
     * @return string
     */
    public function getElasticaView()
    {
        return "KunstmaanMediaPagePartBundle:ImagePagePart:elastica.html.twig";
    }

    /**
     * @return ImagePagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new ImagePagePartAdminType();
    }
}
