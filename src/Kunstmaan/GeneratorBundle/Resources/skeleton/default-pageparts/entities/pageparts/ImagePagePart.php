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
class ImagePagePart extends AbstractPagePart
{
    public const IMAGE_ALIGNMENT = [
        'left' => 'left',
        'center' => 'center',
        'right' => 'right',
    ];

    public const IMAGE_WIDTH = [
        'natural' => 'natural',
        'container' => 'container',
        'full width' => 'full_width',
    ];

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="media_id", referencedColumnName="id")
     * @Assert\NotNull()
     */
    private $media;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $caption;

    /**
     * @ORM\Column(name="alt_text", type="string", nullable=true)
     */
    private $altText;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(name="open_in_new_window", type="boolean", nullable=true)
     */
    private $openInNewWindow = false;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull()
     */
    private $alignment;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotNull()
     */
    private $width;

    public function getOpenInNewWindow(): bool
    {
        return $this->openInNewWindow;
    }

    public function setOpenInNewWindow(bool $openInNewWindow): ImagePagePart
    {
        $this->openInNewWindow = $openInNewWindow;

        return $this;
    }

    public function setLink(string $link): ImagePagePart
    {
        $this->link = $link;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setAltText(string $altText): ImagePagePart
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

    public function setMedia(Media $media): ImagePagePart
    {
        $this->media = $media;

        return $this;
    }

    public function setCaption(string $caption): ImagePagePart
    {
        $this->caption = $caption;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getAlignment(): ?string
    {
        return $this->alignment;
    }

    public function setAlignment(string $alignment): ImagePagePart
    {
        $this->alignment = $alignment;

        return $this;
    }

    public function getWidth(): ?string
    {
        return $this->width;
    }

    public function setWidth(string $width): ImagePagePart
    {
        $this->width = $width;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/image_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
