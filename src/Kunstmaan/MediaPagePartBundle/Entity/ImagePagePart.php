<?php

namespace Kunstmaan\MediaPagePartBundle\Entity;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaPagePartBundle\Form\ImagePagePartAdminType;

/**
 * ImagePagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="pagepart_image")
 */
class ImagePagePart extends AbstractPagePart
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $link;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $openinnewwindow;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $alttext;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     */
    public $media;

    /**
     * Get opennewwindow
     *
     * @return bool
     */
    public function getOpenInNewWindow()
    {
        return $this->openinnewwindow;
    }

    /**
     * Set openwinnewwindow
     *
     * @param bool $link
     */
    public function setOpenInNewWindow($link)
    {
        $this->openinnewwindow = $link;
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
     * @param string $alttext
     */
    public function setAlttext($alttext)
    {
        $this->alttext = $alttext;
    }

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
     * Get alt text
     *
     * @return string
     */
    public function getAlttext()
    {
        return $this->alttext;
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
        return "KunstmaanMediaPagePartBundle:ImagePagePart:view.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getElasticaView()
    {
        return "KunstmaanMediaPagePartBundle:ImagePagePart:elastica.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new ImagePagePartAdminType();
    }
}
