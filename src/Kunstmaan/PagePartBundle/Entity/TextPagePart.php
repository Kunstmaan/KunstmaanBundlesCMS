<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\TextPagePartAdminType;

/**
 * Class that defines a text page part object to add to a page
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_text_page_parts")
 */
class TextPagePart extends AbstractPagePart
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
     * @assert () == "TextPagePart " . $this->object->getContent()
     *
     * @return string
     */
    public function __toString()
    {
        return "TextPagePart " . $this->getContent();
    }

    /**
     * @assert () == 'KunstmaanPagePartBundle:TextPagePart:view.html.twig'
     *
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:TextPagePart:view.html.twig";
    }

    /**
     * @return TextPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new TextPagePartAdminType();
    }
}
