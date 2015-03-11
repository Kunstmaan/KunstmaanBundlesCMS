<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use {{ namespace }}\Entity\PageParts\AbstractPagePart;
use Symfony\Component\Validator\Constraint as Assert;

/**
 * {{ pagepart }}
 *
 * @ORM\Table(name="kuma_{{ pagepartname }}_page_parts")
 * @ORM\Entity
 */
class {{ pagepart }} extends AbstractPagePart
{
    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     */
    protected $url;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $openinnewwindow;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     */
    protected $text;

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
        return $this->openinnewwindow;
    }

    /**
     * @param boolean $openInNewWindow
     *
     * @return {{ pagepart }}
     */
    public function setOpenInNewWindow($openInNewWindow)
    {
        $this->openinnewwindow = $openInNewWindow;

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
     * @return string
     */
    public function __toString()
    {
        return "{{ pagepart }}";
    }
}