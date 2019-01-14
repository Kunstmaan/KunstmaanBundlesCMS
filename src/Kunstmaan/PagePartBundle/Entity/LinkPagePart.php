<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType;

/**
 * LinkPagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_link_page_parts")
 */
class LinkPagePart extends AbstractPagePart
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $openinnewwindow;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $text;

    /**
     * @param string $url
     *
     * @return LinkPagePart
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
     * @return bool
     */
    public function getOpenInNewWindow()
    {
        return $this->openinnewwindow;
    }

    /**
     * @param bool $openInNewWindow
     *
     * @return LinkPagePart
     */
    public function setOpenInNewWindow($openInNewWindow)
    {
        $this->openinnewwindow = $openInNewWindow;

        return $this;
    }

    /**
     * @param string $text
     *
     * @return LinkPagePart
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
        return 'LinkPagePart';
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return 'KunstmaanPagePartBundle:LinkPagePart:view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return LinkPagePartAdminType::class;
    }
}
