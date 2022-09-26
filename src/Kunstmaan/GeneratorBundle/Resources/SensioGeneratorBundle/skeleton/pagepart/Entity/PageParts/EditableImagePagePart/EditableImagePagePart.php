<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\EditableMediaWrapper;
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
     * @var EditableMediaWrapper|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\EditableMediaWrapper", cascade={"persist"})
     * @ORM\JoinColumn(name="media_wrapper_id", referencedColumnName="id")
{% if canUseAttributes == false %}
     * @Assert\Valid()
{% endif %}
{% endif %}
     */
{% if canUseAttributes %}
    #[Assert\Valid]
{% endif %}
{% if canUseEntityAttributes %}
    #[ORM\ManyToOne(targetEntity: EditableMediaWrapper::class, cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'media_wrapper_id', referencedColumnName: 'id')]
{% endif %}
    private $mediaWrapper;

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

    public function setOpenInNewWindow(bool $openInNewWindow): EditableImagePagePart
    {
        $this->openInNewWindow = $openInNewWindow;

        return $this;
    }

    public function setLink(?string $link): EditableImagePagePart
    {
        $this->link = $link;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setAltText(?string $altText): EditableImagePagePart
    {
        $this->altText = $altText;

        return $this;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function getMediaWrapper(): ?EditableMediaWrapper
    {
        return $this->mediaWrapper;
    }

    public function setMediaWrapper(?EditableMediaWrapper $mediaWrapper): EditableImagePagePart
    {
        $this->mediaWrapper = $mediaWrapper;

        return $this;
    }

    public function setCaption(?string $caption): EditableImagePagePart
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
