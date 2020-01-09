<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ admin_type_full }};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="{{ table_name }}")
 * @ORM\Entity
 */
class ButtonPagePart extends AbstractPagePart
{
    /**
     * @ORM\Column(name="link_text", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $linkText;

    /**
     * @ORM\Column(name="link_url", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $linkUrl;

    /**
     * @ORM\Column(name="link_new_window", type="boolean", nullable=true)
     */
    private $linkNewWindow = false;

    /**
     * @ORM\Column(name="type", type="string", length=15, nullable=true)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @ORM\Column(name="size", type="string", length=15, nullable=true)
     * @Assert\NotBlank()
     */
    private $size;

    /**
     * @ORM\Column(name="position", type="string", length=15, nullable=true)
     * @Assert\NotBlank()
     */
    private $position;

    public const TYPE_PRIMARY = 'primary';
    public const TYPE_SECONDARY = 'secondary';
    public const TYPE_EYECATCHER = 'eye-catcher';
    public const TYPE_LINK = 'link';

    public const SIZE_LARGE = 'lg';
    public const SIZE_DEFAULT = 'md';
    public const SIZE_SMALL = 'sm';

    public const POSITION_LEFT = 'left';
    public const POSITION_CENTER = 'center';
    public const POSITION_RIGHT = 'right';
    public const POSITION_BLOCK = 'block';

    /**
     * @var array Supported types
     */
    public static $types = [
        self::TYPE_PRIMARY,
        self::TYPE_SECONDARY,
        self::TYPE_EYECATCHER,
        self::TYPE_LINK,
    ];

    /**
     * @var array Supported sizes
     */
    public static $sizes = [
        self::SIZE_LARGE,
        self::SIZE_DEFAULT,
        self::SIZE_SMALL,
    ];

    /**
     * @var array Supported positions
     */
    public static $positions = [
        self::POSITION_LEFT,
        self::POSITION_CENTER,
        self::POSITION_RIGHT,
        self::POSITION_BLOCK,
    ];

    public function __construct()
    {
        $this->type = self::TYPE_PRIMARY;
        $this->size = self::SIZE_DEFAULT;
        $this->position = self::POSITION_LEFT;
    }

    public function setLinkNewWindow(bool $linkNewWindow): ButtonPagePart
    {
        $this->linkNewWindow = $linkNewWindow;

        return $this;
    }

    public function isLinkNewWindow(): bool
    {
        return $this->linkNewWindow;
    }

    public function setLinkText(string $linkText): ButtonPagePart
    {
        $this->linkText = $linkText;

        return $this;
    }

    public function getLinkText(): ?string
    {
        return $this->linkText;
    }

    public function setLinkUrl(?string $linkUrl): ButtonPagePart
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    public function getLinkUrl(): ?string
    {
        return $this->linkUrl;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setType(string $type): ButtonPagePart
    {
        if (!in_array($type, self::$types, true)) {
            throw new \InvalidArgumentException(sprintf('Type "%s" not supported', $type));
        }
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setSize(string $size): ButtonPagePart
    {
        if (!in_array($size, self::$sizes, true)) {
            throw new \InvalidArgumentException(sprintf('Size "%s" not supported', $size));
        }
        $this->size = $size;

        return $this;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function setPosition(string $position): ButtonPagePart
    {
        if (!in_array($position, self::$positions, true)) {
            throw new \InvalidArgumentException(sprintf('Position "%s" not supported', $position));
        }
        $this->position = $position;

        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function getDefaultView(): string
    {
        return 'pageparts/button_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
