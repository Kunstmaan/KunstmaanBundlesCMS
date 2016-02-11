<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ServicePagePart
 *
 * @ORM\Table(name="{{ prefix }}service_page_parts")
 * @ORM\Entity
 */
class ServicePagePart extends AbstractPagePart
{
    const IMAGE_POSITION_LEFT = 'left';
    const IMAGE_POSITION_RIGHT = 'right';

    /**
     * @var array Supported positions
     */
    public static $imagePositions = array(
	self::IMAGE_POSITION_LEFT,
	self::IMAGE_POSITION_RIGHT
    );

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="link_url", type="string", nullable=true)
     */
    private $linkUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="link_text", type="string", nullable=true)
     */
    private $linkText;

    /**
     * @var boolean
     *
     * @ORM\Column(name="link_new_window", type="boolean", nullable=true)
     */
    private $linkNewWindow;

    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="image_position", type="string", length=15, nullable=true)
     * @Assert\NotBlank()
     */
    private $imagePosition;

    /**
     * Set title
     *
     * @param string $title
     * @return ServicePagePart
     */
    public function setTitle($title)
    {
	$this->title = $title;

	return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
	return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return ServicePagePart
     */
    public function setDescription($description)
    {
	$this->description = $description;

	return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
	return $this->description;
    }

    /**
     * Set linkUrl
     *
     * @param string $linkUrl
     * @return ServicePagePart
     */
    public function setLinkUrl($linkUrl)
    {
	$this->linkUrl = $linkUrl;

	return $this;
    }

    /**
     * Get linkUrl
     *
     * @return string
     */
    public function getLinkUrl()
    {
	return $this->linkUrl;
    }

    /**
     * Set linkText
     *
     * @param string $linkText
     * @return ServicePagePart
     */
    public function setLinkText($linkText)
    {
	$this->linkText = $linkText;

	return $this;
    }

    /**
     * Get linkText
     *
     * @return string
     */
    public function getLinkText()
    {
	return $this->linkText;
    }

    /**
     * Set linkNewWindow
     *
     * @param boolean $linkNewWindow
     * @return ServicePagePart
     */
    public function setLinkNewWindow($linkNewWindow)
    {
	$this->linkNewWindow = $linkNewWindow;

	return $this;
    }

    /**
     * Get linkNewWindow
     *
     * @return boolean
     */
    public function getLinkNewWindow()
    {
	return $this->linkNewWindow;
    }

    /**
     * Set image
     *
     * @param Media $image
     * @return ServicePagePart
     */
    public function setImage(Media $image = null)
    {
	$this->image = $image;

	return $this;
    }

    /**
     * Get image
     *
     * @return Media
     */
    public function getImage()
    {
	return $this->image;
    }

    /**
     * @return string
     */
    public function getImagePosition()
    {
	return $this->imagePosition;
    }

    /**
     * @param string $imagePosition
     *
     * @return ServicePagePart
     */
    public function setImagePosition($imagePosition)
    {
	$this->imagePosition = $imagePosition;

	return $this;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return '{{ bundle.getName() }}:PageParts:ServicePagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \{{ namespace }}\Form\PageParts\ServicePagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \{{ namespace }}\Form\PageParts\ServicePagePartAdminType();
    }
}
