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
class MediaWithParagraphPagePart extends AbstractPagePart
{
    public const IMAGE_POSITION = [
        'left' => 'left',
        'right' => 'right',
    ];

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     * })
     * @Assert\NotBlank()
     */
    private $image;

    /**
     * @ORM\Column(name="image_position", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $imagePosition;

    /**
     * @ORM\Column(name="image_alt_text", type="text", nullable=true)
     */
    private $imageAltText;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $wrap = true;

    public function getImagePosition(): ?string
    {
        return $this->imagePosition;
    }

    public function setImagePosition(string $imagePosition): MediaWithParagraphPagePart
    {
        $this->imagePosition = $imagePosition;

        return $this;
    }

    public function setImageAltText(string $imageAltText): MediaWithParagraphPagePart
    {
        $this->imageAltText = $imageAltText;

        return $this;
    }

    public function getImageAltText(): ?string
    {
        return $this->imageAltText;
    }

    public function setText(string $text): MediaWithParagraphPagePart
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setImage(Media $image): MediaWithParagraphPagePart
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function getWrap(): bool
    {
        return $this->wrap;
    }

    public function setWrap(bool $wrap): MediaWithParagraphPagePart
    {
        $this->wrap = $wrap;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/media_with_paragraph_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
