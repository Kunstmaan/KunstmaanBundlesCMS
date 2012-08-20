<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\LinkPagePartAdminType;

/**
 * LinkPagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="linkpagepart")
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
     */
    public function setUrl($url)
    {
        $this->url = $url;
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
     */
    public function setOpenInNewWindow($openInNewWindow)
    {
        $this->openinnewwindow = $openInNewWindow;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
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
        return "LinkPagePart";
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:LinkPagePart:view.html.twig";
    }

    /**
     * @return string
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * @return LinkPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new LinkPagePartAdminType();
    }
}
