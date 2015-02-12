<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\ButtonPagePartAdminType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ButtonPagePart
 *
 * @ORM\Table(name="kuma_button_page_parts")
 * @ORM\Entity
 */
class ButtonPagePart extends AbstractPagePart
{
    /**
     * @var string
     *
     * @ORM\Column(name="link_text", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $linkText;

    /**
     * @var string
     *
     * @ORM\Column(name="link_url", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $linkUrl;

    /**
     * @var boolean
     *
     * @ORM\Column(name="link_new_window", type="boolean", nullable=true)
     */
    private $linkNewWindow;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $type = 'primary';

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     */
    private $size = 'default';

    /**
     * @var boolean
     *
     * @ORM\Column(name="block", type="boolean", nullable=true)
     */
    private $block = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="icon", type="boolean", nullable=true)
     */
    private $icon = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="center", type="boolean", nullable=true)
     */
    private $center = false;

    const TYPE_PRIMARY = "primary";
    const TYPE_SECONDARY = "secondary";
    const TYPE_TERTIARY = "tertiary";
    const TYPE_QUATERNARY = "quaternary";
    const TYPE_LINK = "link";

    const SIZE_EXTRA_LARGE = "xl";
    const SIZE_LARGE = "lg";
    const SIZE_DEFAULT = "default";
    const SIZE_SMALL = "sm";
    const SIZE_EXTRA_SMALL = "xs";

    /**
     * @var array Supported types
     */
    public static $types = array(self::TYPE_PRIMARY,self::TYPE_SECONDARY,self::TYPE_TERTIARY,self::TYPE_QUATERNARY,self::TYPE_LINK);

    /**
     * @var array Supported sizes
     */
    public static $sizes = array(self::SIZE_EXTRA_LARGE,self::SIZE_LARGE,self::SIZE_DEFAULT,self::SIZE_SMALL,self::SIZE_EXTRA_SMALL);

    /**
     * @param boolean $linkNewWindow
     *
     * @return ButtonPagePart
     */
    public function setLinkNewWindow($linkNewWindow)
    {
        $this->linkNewWindow = $linkNewWindow;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isLinkNewWindow()
    {
        return $this->linkNewWindow;
    }

    /**
     * @param string $linkText
     *
     * @return ButtonPagePart
     */
    public function setLinkText($linkText)
    {
        $this->linkText = $linkText;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkText()
    {
        return $this->linkText;
    }

    /**
     * @param string $linkUrl
     *
     * @return ButtonPagePart
     */
    public function setLinkUrl($linkUrl)
    {
        $this->linkUrl = $linkUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return ButtonPagePart
     */
    public function setType($type)
    {
        if (!in_array($type, self::$types)) {
            $type = 'primary';
        }
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set size
     *
     * @param string $size
     * @return ButtonPagePart
     */
    public function setSize($size)
    {
        if (!in_array($size, self::sizes)) {
            $size = 'default';
        }
        $this->size = $size;

        return $this;
    }

    /**
     * Get size
     *
     * @return string
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set block
     *
     * @param boolean $block
     * @return ButtonPagePart
     */
    public function setBlock($block)
    {
        $this->block = $block;

        return $this;
    }

    /**
     * Get block
     *
     * @return boolean
     */
    public function getBlock()
    {
        return $this->block;
    }

    /**
     * Set icon
     *
     * @param boolean $icon
     * @return ButtonPagePart
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon
     *
     * @return boolean
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return boolean
     */
    public function isCenter()
    {
        return $this->center;
    }

    /**
     * @param boolean $center
     */
    public function setCenter($center)
    {
        $this->center = $center;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:ButtonPagePart:view.html.twig";
    }

    /**
     * Get the admin form type.
     *
     * @return \Kunstmaan\PagePartBundle\Form\ButtonPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new ButtonPagePartAdminType();
    }
}