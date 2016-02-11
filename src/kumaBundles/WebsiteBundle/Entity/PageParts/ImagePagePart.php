<?php

namespace kumaBundles\WebsiteBundle\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ImagePagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="kubu_image_page_parts")
 */
class ImagePagePart extends AbstractPagePart
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $media;

    /**
     * @ORM\Column(type="string", name="caption", nullable=true)
     */
    private $caption;

    /**
     * @ORM\Column(type="string", name="alt_text", nullable=true)
     */
    private $altText;

    /**
     * @ORM\Column(name="link", type="string", nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(name="open_in_new_window", type="boolean", nullable=true)
     */
    private $openInNewWindow;

    /**
     * Get open in new window
     *
     * @return bool
     */
    public function getOpenInNewWindow()
    {
	return $this->openInNewWindow;
    }

    /**
     * Set open in new window
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
     * Get alt text
     *
     * @return string
     */
    public function getAltText()
    {
	return $this->altText;
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
    public function setMedia(Media $media)
    {
	$this->media = $media;

	return $this;
    }

    /**
     * Set caption
     *
     * @param string $caption
     *
     * @return ImagePagePart
     */
    public function setCaption($caption)
    {
	$this->caption = $caption;

	return $this;
    }

    /**
     * Get caption
     *
     * @return string
     */
    public function getCaption()
    {
	return $this->caption;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return 'kumaBundlesWebsiteBundle:PageParts:ImagePagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \kumaBundles\WebsiteBundle\Form\PageParts\ImagePagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \kumaBundles\WebsiteBundle\Form\PageParts\ImagePagePartAdminType();
    }
}
