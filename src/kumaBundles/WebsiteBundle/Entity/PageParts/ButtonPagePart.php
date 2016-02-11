<?php

namespace kumaBundles\WebsiteBundle\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ButtonPagePart
 *
 * @ORM\Table(name="kubu_button_page_parts")
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
     * @ORM\Column(name="type", type="string", length=15, nullable=true)
     * @Assert\NotBlank()
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="size", type="string", length=15, nullable=true)
     * @Assert\NotBlank()
     */
    private $size;

    /**
     * @var string
     *
     * @ORM\Column(name="position", type="string", length=15, nullable=true)
     * @Assert\NotBlank()
     */
    private $position;

    const TYPE_PRIMARY = 'primary';
    const TYPE_SECONDARY = 'secondary';
    const TYPE_TERTIARY = 'tertiary';
    const TYPE_QUATERNARY = 'quaternary';
    const TYPE_LINK = 'link';

    const SIZE_EXTRA_LARGE = 'xl';
    const SIZE_LARGE = 'lg';
    const SIZE_DEFAULT = 'default';
    const SIZE_SMALL = 'sm';
    const SIZE_EXTRA_SMALL = 'xs';

    const POSITION_LEFT = 'left';
    const POSITION_CENTER = 'center';
    const POSITION_RIGHT = 'right';
    const POSITION_BLOCK = 'block';

    /**
     * @var array Supported types
     */
    public static $types = array(
	self::TYPE_PRIMARY,
	self::TYPE_SECONDARY,
	self::TYPE_TERTIARY,
	self::TYPE_QUATERNARY,
	self::TYPE_LINK
    );

    /**
     * @var array Supported sizes
     */
    public static $sizes = array(
	self::SIZE_EXTRA_LARGE,
	self::SIZE_LARGE,
	self::SIZE_DEFAULT,
	self::SIZE_SMALL,
	self::SIZE_EXTRA_SMALL
    );

    /**
     * @var array Supported positions
     */
    public static $positions = array(
	self::POSITION_LEFT,
	self::POSITION_CENTER,
	self::POSITION_RIGHT,
	self::POSITION_BLOCK
    );

    public function __construct()
    {
	$this->type = self::TYPE_PRIMARY;
	$this->size = self::SIZE_DEFAULT;
	$this->position = self::POSITION_LEFT;
    }

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
     * @throws \InvalidArgumentException
     */
    public function setType($type)
    {
	if (!in_array($type, self::$types)) {
	    throw new \InvalidArgumentException("Type $type not supported");
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
     * @throws \InvalidArgumentException
     */
    public function setSize($size)
    {
	if (!in_array($size, self::$sizes)) {
	    throw new \InvalidArgumentException("Size $size not supported");
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
     * Set position
     *
     * @param string $position
     * @return ButtonPagePart
     * @throws \InvalidArgumentException
     */
    public function setPosition($position)
    {
	if (!in_array($position, self::$positions)) {
	    throw new \InvalidArgumentException("Position $position not supported");
	}
	$this->position = $position;

	return $this;
    }

    /**
     * Get position
     *
     * @return string
     */
    public function getPosition()
    {
	return $this->position;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return 'kumaBundlesWebsiteBundle:PageParts:ButtonPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \kumaBundles\WebsiteBundle\Form\PageParts\ButtonPagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \kumaBundles\WebsiteBundle\Form\PageParts\ButtonPagePartAdminType();
    }
}
