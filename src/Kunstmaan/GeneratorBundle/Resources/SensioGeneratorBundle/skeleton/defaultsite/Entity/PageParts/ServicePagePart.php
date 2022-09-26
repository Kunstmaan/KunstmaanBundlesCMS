<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ namespace }}\Form\PageParts\ServicePagePartAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}service_page_parts')]
{% else %}
/**
 * @ORM\Table(name="{{ prefix }}service_page_parts")
 * @ORM\Entity
 */
{% endif %}
class ServicePagePart extends AbstractPagePart
{
    public const IMAGE_POSITION_LEFT = 'left';
    public const IMAGE_POSITION_RIGHT = 'right';

    /**
     * @var array Supported positions
     */
    public static $imagePositions = [
        self::IMAGE_POSITION_LEFT,
        self::IMAGE_POSITION_RIGHT,
    ];

    /**
     * @var string|null
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
     * @var string|null
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
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="link_url", type="string", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'link_url', type: 'string', nullable: true)]
{% endif %}
    private $linkUrl;

    /**
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="link_text", type="string", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'link_text', type: 'string', nullable: true)]
{% endif %}
    private $linkText;

    /**
     * @var bool
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="link_new_window", type="boolean", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'link_new_window', type: 'boolean', nullable: true)]
{% endif %}
    private $linkNewWindow = false;

    /**
     * @var Media|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'image_id', referencedColumnName: 'id')]
{% endif %}
    private $image;

    /**
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="image_position", type="string", length=15, nullable=true)
{% if canUseAttributes == false %}
     * @Assert\NotBlank()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotBlank]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'image_position', type: 'string', length: 15, nullable: true)]
{% endif %}
    private $imagePosition;

    public function setTitle(?string $title): ServicePagePart
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(?string $description): ServicePagePart
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setLinkUrl(?string $linkUrl): ServicePagePart
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkText(?string $linkText): ServicePagePart
    {
        $this->linkText = $linkText;

        return $this;
    }

    public function getLinkText(): ?string
    {
        return $this->linkText;
    }

    public function setLinkNewWindow(bool $linkNewWindow): ServicePagePart
    {
        $this->linkNewWindow = $linkNewWindow;

        return $this;
    }

    public function getLinkNewWindow(): bool
    {
        return $this->linkNewWindow;
    }

    public function setImage(?Media $image): ServicePagePart
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function getImagePosition(): ?string
    {
        return $this->imagePosition;
    }

    public function setImagePosition(?string $imagePosition): ServicePagePart
    {
        $this->imagePosition = $imagePosition;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'PageParts/ServicePagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return ServicePagePartAdminType::class;
    }
}
