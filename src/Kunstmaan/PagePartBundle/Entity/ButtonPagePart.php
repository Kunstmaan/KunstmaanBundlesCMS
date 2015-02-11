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

    /**
     * @var array Supported types
     */
    public static $TYPES = array('primary','secondary','tertiary','quaternary','quinary','link');

    /**
     * @var array Supported sizes
     */
    public static $SIZES = array('xl','lg','default','sm','xs');

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
        if (!in_array($type, self::$TYPES)) {
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
        if (!in_array($size, self::$SIZES)) {
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

    /**
     * @return string
     */
    public function getCssClasses()
    {
        $classes = array('btn');
        $classes[] = 'btn--' . $this->type;
        if ($this->size !== 'default') {
            $classes[] = 'btn--' . $this->size;
        }
        if ($this->block) {
            $classes[] = 'btn--block';
        }

        return implode(' ', $classes);
    }
}