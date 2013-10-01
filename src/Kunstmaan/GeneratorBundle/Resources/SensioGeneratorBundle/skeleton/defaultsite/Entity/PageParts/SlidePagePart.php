<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;

/**
 * SlidePagePart
 *
 * @ORM\Table(name="{{ prefix }}slide_page_part")
 * @ORM\Entity
 */
class SlidePagePart extends \Kunstmaan\PagePartBundle\Entity\AbstractPagePart
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
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
     * @ORM\Column(name="tick_text", type="string", length=255, nullable=true)
     */
    private $tickText;

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
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     */
    private $image;

    /**
     * Set title
     *
     * @param string $title
     * @return SlidePagePart
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
     * @return SlidePagePart
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
     * Set tickText
     *
     * @param string $tickText
     * @return SlidePagePart
     */
    public function setTickText($tickText)
    {
        $this->tickText = $tickText;

        return $this;
    }

    /**
     * Get tickText
     *
     * @return string
     */
    public function getTickText()
    {
        return $this->tickText;
    }

    /**
     * Set buttonUrl
     *
     * @param string $buttonUrl
     * @return SlidePagePart
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
     * @return SlidePagePart
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
     * @return SlidePagePart
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
     * Set image
     *
     * @param \Kunstmaan\MediaBundle\Entity\Media $image
     * @return SlidePagePart
     */
    public function setImage(\Kunstmaan\MediaBundle\Entity\Media $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \Kunstmaan\MediaBundle\Entity\Media
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle.getName() }}:PageParts:SlidePagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \{{ namespace }}\Form\Pageparts\SlidePagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new \{{ namespace }}\Form\PageParts\SlidePagePartAdminType();
    }
}