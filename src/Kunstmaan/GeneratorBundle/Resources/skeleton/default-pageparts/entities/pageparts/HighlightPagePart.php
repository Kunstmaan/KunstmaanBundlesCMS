<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ admin_type_full }};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ table_name }}")
 * @ORM\Entity
 */
class HighlightPagePart extends AbstractPagePart
{
    /**
     * @ORM\Column(name="title", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @ORM\Column(name="link_text", type="string", length=255, nullable=true)
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): HighlightPagePart
    {
        $this->title = $title;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): HighlightPagePart
    {
        $this->text = $text;

        return $this;
    }

    public function getLinkText(): ?string
    {
        return $this->linkText;
    }

    public function setLinkText($linkText): HighlightPagePart
    {
        $this->linkText = $linkText;

        return $this;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    public function setLinkUrl($linkUrl): HighlightPagePart
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    public function getLinkNewWindow(): bool
    {
        return $this->linkNewWindow;
    }

    public function setLinkNewWindow(bool $linkNewWindow): HighlightPagePart
    {
        $this->linkNewWindow = $linkNewWindow;

        return $this;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/highlight_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
