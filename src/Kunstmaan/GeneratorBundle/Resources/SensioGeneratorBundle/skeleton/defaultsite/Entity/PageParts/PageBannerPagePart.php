<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ namespace }}\Form\PageParts\PageBannerPagePartAdminType;
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
     * @ORM\Column(name="button_url", type="string", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'button_url', type: 'string', nullable: true)]
{% endif %}
    private $buttonUrl;

    /**
     * @var string|null
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
     * @var bool
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="button_new_window", type="boolean", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'button_new_window', type: 'boolean', nullable: true)]
{% endif %}
    private $buttonNewWindow = false;

    /**
     * @var Media|null
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

    public function setTitle(?string $title): PageBannerPagePart
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(?string $description): PageBannerPagePart
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setButtonUrl(?string $buttonUrl): PageBannerPagePart
    {
        $this->buttonUrl = $buttonUrl;

        return $this;
    }

    public function getButtonUrl(): ?string
    {
        return $this->buttonUrl;
    }

    public function setButtonText(?string $buttonText): PageBannerPagePart
    {
        $this->buttonText = $buttonText;

        return $this;
    }

    public function getButtonText(): ?string
    {
        return $this->buttonText;
    }

    public function setButtonNewWindow(bool $buttonNewWindow): PageBannerPagePart
    {
        $this->buttonNewWindow = $buttonNewWindow;

        return $this;
    }

    public function getButtonNewWindow(): bool
    {
        return $this->buttonNewWindow;
    }

    public function setBackgroundImage(?Media $backgroundImage): PageBannerPagePart
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    public function getBackgroundImage(): ?Media
    {
        return $this->backgroundImage;
    }

    public function getDefaultView(): string
    {
        return 'PageParts/PageBannerPagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return PageBannerPagePartAdminType::class;
    }
}
