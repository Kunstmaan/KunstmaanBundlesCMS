<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}page_banner_page_parts')]
{% else %}
/**
 * @ORM\Table(name="{{ prefix }}page_banner_page_parts")
 * @ORM\Entity
 */
{% endif %}
class PageBannerPagePart extends AbstractPagePart
{
    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'title', type: 'string', length: 255, nullable: true)]
{% endif %}
    private $title;

    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="description", type="text", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
{% endif %}
    private $description;

    /**
     * @var string
{% if canUseEntityAttributes == false %}
    *
    * @ORM\Column(name="button_url", type="string", nullable=true)
{% endif %}
    */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'button_url', type: 'string', nullable: true)]
{% endif %}
    private $buttonUrl;

    /**
     * @var string
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="button_text", type="string", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'button_text', type: 'string', nullable: true)]
{% endif %}
    private $buttonText;

    /**
     * @var boolean
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="button_new_window", type="boolean", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'button_new_window', type: 'boolean', nullable: true)]
{% endif %}
    private $buttonNewWindow;

    /**
     * @var Media
{% if canUseEntityAttributes == false %}
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="background_id", referencedColumnName="id")
     * })
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'background_id', referencedColumnName: 'id')]
{% endif %}
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
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}PageParts/PageBannerPagePart{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
	return \{{ namespace }}\Form\PageParts\PageBannerPagePartAdminType::class;
    }
}
