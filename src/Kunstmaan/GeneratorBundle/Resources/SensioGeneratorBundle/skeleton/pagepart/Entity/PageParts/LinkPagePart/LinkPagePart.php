<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * {{ pagepart }}
 *
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 * @ORM\Entity
 */
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @ORM\Column(name="url", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $url;

    /**
     * @ORM\Column(name="text", type="string", nullable=true)
     * @Assert\NotBlank()
     */
    private $text;

    /**
     * @ORM\Column(name="open_in_new_window", type="boolean", nullable=true)
     */
    private $openInNewWindow;

    /**
     * @param string $url
     *
     * @return {{ pagepart }}
     */
    public function setUrl($url)
    {
	$this->url = $url;

	return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
	return $this->url;
    }

    /**
     * @return boolean
     */
    public function getOpenInNewWindow()
    {
	return $this->openInNewWindow;
    }

    /**
     * @param boolean $openInNewWindow
     *
     * @return {{ pagepart }}
     */
    public function setOpenInNewWindow($openInNewWindow)
    {
	$this->openInNewWindow = $openInNewWindow;

	return $this;
    }

    /**
     * @param string $text
     *
     * @return {{ pagepart }}
     */
    public function setText($text)
    {
	$this->text = $text;

	return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
	return $this->text;
    }

    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
	return '{{ bundle }}:PageParts:{{ pagepart }}/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return {{ adminType }}
     */
    public function getDefaultAdminType()
    {
	return new {{ adminType }}();
    }
}
