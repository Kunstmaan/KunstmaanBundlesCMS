<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ admin_type_full }};
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="{{ table_name }}")
 */
class VideoPagePart extends AbstractPagePart
{
    public const VIDEO_WIDTH = [
        'container' => 'container',
        'full width' => 'full_width',
    ];

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="video_media_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $video;

    /**
     * @ORM\Column(type="string", name="caption", nullable=true)
     */
    private $caption;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="thumbnail_media_id", referencedColumnName="id")
     * })
     */
    private $thumbnail;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull()
     */
    private $width;

    public function setCaption(string $caption): VideoPagePart
    {
        $this->caption = $caption;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function setThumbnail(Media $thumbnail): VideoPagePart
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    public function getThumbnail(): ?Media
    {
        return $this->thumbnail;
    }

    public function setVideo(Media $video): VideoPagePart
    {
        $this->video = $video;

        return $this;
    }

    public function getVideo(): ?Media
    {
        return $this->video;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): VideoPagePart
    {
        $this->width = $width;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/video_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
