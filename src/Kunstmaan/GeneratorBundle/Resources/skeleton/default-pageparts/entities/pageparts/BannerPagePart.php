<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ admin_type_full }};
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\MediaBundle\Entity\Media;

/**
 * @ORM\Entity
 * @ORM\Table(name="{{ table_name }}")
 */
class BannerPagePart extends AbstractPagePart
{
    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
     */
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\MediaBundle\Entity\Media")
     * @ORM\JoinColumn(name="background_image_id", referencedColumnName="id")
     */
    private $backgroundImage;

    /**
     * @ORM\Column(name="button_link", type="string", nullable=true)
     */
    private $buttonLink;

    /**
     * @ORM\Column(name="button_text", type="string", nullable=true)
     */
    private $buttonText;

    /**
     * @ORM\Column(name="open_in_new_window", type="boolean", nullable=true)
     */
    private $openInNewWindow = false;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $text;

    public function getOpenInNewWindow(): bool
    {
        return $this->openInNewWindow;
    }

    public function setOpenInNewWindow(bool $openInNewWindow): BannerPagePart
    {
        $this->openInNewWindow = $openInNewWindow;

        return $this;
    }

    public function setButtonLink(string $buttonLink): BannerPagePart
    {
        $this->buttonLink = $buttonLink;

        return $this;
    }

    public function getButtonLink(): ?string
    {
        return $this->buttonLink;
    }

    public function setButtonText(string $buttonText): BannerPagePart
    {
        $this->buttonText = $buttonText;

        return $this;
    }

    public function getButtonText(): ?string
    {
        return $this->buttonText;
    }

    public function getBackgroundImage(): ?Media
    {
        return $this->backgroundImage;
    }

    public function setBackgroundImage(Media $backgroundImage): BannerPagePart
    {
        $this->backgroundImage = $backgroundImage;

        return $this;
    }

    public function getImage(): ?Media
    {
        return $this->image;
    }

    public function setImage(Media $image): BannerPagePart
    {
        $this->image = $image;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): BannerPagePart
    {
        $this->text = $text;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/banner_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
