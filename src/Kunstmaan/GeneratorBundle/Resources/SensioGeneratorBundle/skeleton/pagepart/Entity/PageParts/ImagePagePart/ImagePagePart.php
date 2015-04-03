<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * {{ pagepart }}
 *
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 */
class {{ pagepart }} extends AbstractPagePart
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
     * @return {{ pagepart }}
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
     * @return {{ pagepart }}
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
     * @return {{ pagepart }}
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
     * @return {{ pagepart }}
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
     * @return {{ pagepart }}
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
	return '{{ bundle }}:PageParts:{{ pagepart }}/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return {{ adminType }}
     */
    public function getDefaultAdminType()
    {
	return new {{ adminType }}();
    }
}
