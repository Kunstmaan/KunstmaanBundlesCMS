<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;

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
{% endif %}
     */
{% if canUseEntityAttributes %}
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'media_id', referencedColumnName: 'id')]
{% endif %}
    protected $media;

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): DownloadPagePart
    {
        $this->media = $media;

        return $this;
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
