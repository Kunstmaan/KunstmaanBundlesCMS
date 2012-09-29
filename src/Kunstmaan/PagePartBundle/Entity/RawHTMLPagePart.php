<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\RawHTMLPagePartAdminType;

/**
 * Class that defines a raw html page part object to add to a page
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_raw_html_page_parts")
 */
class RawHTMLPagePart extends AbstractPagePart
{

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @assert () == "RawHTMLPagePart " . htmlentities($this->object->getContent())
     *
     * @return string
     */
    public function __toString()
    {
        return "RawHTMLPagePart " . htmlentities($this->getContent());
    }

    /**
     * @assert () == 'KunstmaanPagePartBundle:RawHTMLPagePart:view.html.twig'
     *
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:RawHTMLPagePart:view.html.twig";
    }

    /**
     * @return RawHTMLPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new RawHTMLPagePartAdminType();
    }
}
