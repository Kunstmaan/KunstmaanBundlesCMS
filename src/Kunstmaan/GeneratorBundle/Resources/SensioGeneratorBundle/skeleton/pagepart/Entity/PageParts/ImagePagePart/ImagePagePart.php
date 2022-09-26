<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}{{ underscoreName }}s')]
{% else %}
/**
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 */
{% endif %}
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @var Media|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
{% if canUseAttributes == false %}
     * @Assert\NotNull()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\NotNull]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'media_id', referencedColumnName: 'id')]
{% endif %}
    private $media;

    /**
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="string", name="caption", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'caption', type: 'string', nullable: true)]
{% endif %}
    private $caption;

    /**
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(type="string", name="alt_text", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'alt_text', type: 'string', nullable: true)]
{% endif %}
    private $altText;

    /**
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="link", type="string", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'link', type: 'string', nullable: true)]
{% endif %}
    private $link;

    /**
     * @var bool
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="open_in_new_window", type="boolean", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'open_in_new_window', type: 'boolean', nullable: true)]
{% endif %}
    private $openInNewWindow = false;

    public function getOpenInNewWindow(): bool
    {
        return $this->openInNewWindow;
    }

    public function setOpenInNewWindow(bool $openInNewWindow): ImagePagePart
    {
        $this->openInNewWindow = $openInNewWindow;

        return $this;
    }

    public function setLink(?string $link): ImagePagePart
    {
        $this->link = $link;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setAltText(?string $altText): ImagePagePart
    {
        $this->altText = $altText;

        return $this;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): ImagePagePart
    {
        $this->media = $media;

        return $this;
    }

    public function setCaption(?string $caption): ImagePagePart
    {
        $this->caption = $caption;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getDefaultView(): string
    {
        return 'PageParts/{{ pagepart }}/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ adminType }}::class;
    }
}
