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
     * @ORM\JoinColumn(name="video_media_id", referencedColumnName="id")
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
    #[ORM\JoinColumn(name: 'video_media_id', referencedColumnName: 'id')]
{% endif %}
    protected $video;

    /**
     * @var string|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\Column(name="caption", type="string", nullable=true)
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\Column(name: 'caption', type: 'string', nullable: true)]
{% endif %}
    protected $caption;

    /**
     * @var Media|null
{% if canUseEntityAttributes == false %}
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="thumbnail_media_id", referencedColumnName="id")
     * })
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'thumbnail_media_id', referencedColumnName: 'id')]
{% endif %}
    protected $thumbnail;

    public function setCaption(?string $caption): void
    {
        $this->caption = $caption;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setThumbnail(?Media $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getThumbnail(): ?Media
    {
        return $this->thumbnail;
    }

    public function setVideo(?Media $video): void
    {
        $this->video = $video;
    }

    public function getVideo(): ?Media
    {
        return $this->video;
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
