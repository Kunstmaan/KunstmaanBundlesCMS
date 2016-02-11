<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PageBannerPagePart
 *
 * @ORM\Table(name="{{ prefix }}page_banner_page_parts")
 * @ORM\Entity
 */
class PageBannerPagePart extends AbstractPagePart
{
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
     * @ORM\Column(name="button_url", type="string", nullable=true)
     */
    private $buttonUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="button_text", type="string", nullable=true)
     */
    private $buttonText;

    /**
     * @var boolean
     *
     * @ORM\Column(name="button_new_window", type="boolean", nullable=true)
     */
    private $buttonNewWindow;

    /**
     * @var \Kunstmaan\MediaBundle\Entity\Media
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="background_id", referencedColumnName="id")
     * })
     */
    private $backgroundImage;

    /**
     * Set title
     *
     * @param string $title
     * @return PageBannerPagePart
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
     * @return PageBannerPagePart
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
     * Set buttonUrl
     *
     * @param string $buttonUrl
     * @return PageBannerPagePart
     */
    public function setButtonUrl($buttonUrl)
    {
	$this->buttonUrl = $buttonUrl;

	return $this;
    }

    /**
     * Get buttonUrl
     *
     * @return string
     */
    public function getButtonUrl()
    {
	return $this->buttonUrl;
    }

    /**
     * Set buttonText
     *
     * @param string $buttonText
     * @return PageBannerPagePart
     */
    public function setButtonText($buttonText)
    {
	$this->buttonText = $buttonText;

	return $this;
    }

    /**
     * Get buttonText
     *
     * @return string
     */
    public function getButtonText()
    {
	return $this->buttonText;
    }

    /**
     * Set buttonNewWindow
     *
     * @param boolean $buttonNewWindow
     * @return PageBannerPagePart
     */
    public function setButtonNewWindow($buttonNewWindow)
    {
	$this->buttonNewWindow = $buttonNewWindow;

	return $this;
    }

    /**
     * Get buttonNewWindow
     *
     * @return boolean
     */
    public function getButtonNewWindow()
    {
	return $this->buttonNewWindow;
    }

    /**
     * Set background
     *
     * @param Media $backgroundImage
     * @return PageBannerPagePart
     */
    public function setBackgroundImage(Media $backgroundImage = null)
    {
	$this->backgroundImage = $backgroundImage;

	return $this;
    }

    /**
     * Get background
     *
     * @return Media
     */
    public function getBackgroundImage()
    {
	return $this->backgroundImage;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return '{{ bundle.getName() }}:PageParts:PageBannerPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \{{ namespace }}\Form\PageParts\PageBannerPagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \{{ namespace }}\Form\PageParts\PageBannerPagePartAdminType();
    }
}
