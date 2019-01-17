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
class MediaWithContentPagePart extends AbstractPagePart
{
    public const IMAGE_POSITION = [
        'left' => 'left',
        'right' => 'right',
    ];

    public const CONTENT_ALIGNMENT = [
        'left' => 'left',
        'center' => 'center',
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
     * @ORM\Column(name="content_alignment", type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $contentAlignment;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    /**
     * @ORM\Column(name="link_text", type="string", nullable=true)
     */
    private $linkText;

    /**
     * @ORM\Column(name="link_url", type="string", nullable=true)
     */
    private $linkUrl;

    /**
     * @ORM\Column(name="link_new_window", type="boolean", nullable=true)
     */
    private $linkNewWindow = false;

    public function getImagePosition(): ?string
    {
        return $this->imagePosition;
    }

    public function setImagePosition(string $imagePosition): MediaWithContentPagePart
    {
        $this->imagePosition = $imagePosition;

        return $this;
    }

    public function setImageAltText(string $imageAltText): MediaWithContentPagePart
    {
        $this->imageAltText = $imageAltText;

        return $this;
    }

    public function getImageAltText(): ?string
    {
        return $this->imageAltText;
    }

    public function setTitle(string $title): MediaWithContentPagePart
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setText(string $text): MediaWithContentPagePart
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setImage(Media $image): MediaWithContentPagePart
    {
        $this->image = $image;

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function getContentAlignment(): ?string
    {
        return $this->contentAlignment;
    }

    public function setContentAlignment(string $contentAlignment): MediaWithContentPagePart
    {
        $this->contentAlignment = $contentAlignment;

        return $this;
    }

    public function setLinkNewWindow(bool $linkNewWindow): MediaWithContentPagePart
    {
        $this->linkNewWindow = $linkNewWindow;

        return $this;
    }

    public function isLinkNewWindow(): bool
    {
        return $this->linkNewWindow;
    }

    public function setLinkText(string $linkText): MediaWithContentPagePart
    {
        $this->linkText = $linkText;

        return $this;
    }

    public function getLinkText(): ?string
    {
        return $this->linkText;
    }

    public function setLinkUrl(string $linkUrl): MediaWithContentPagePart
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/media_with_content_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
