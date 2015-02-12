<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\IntroTextPagePartAdminType;

/**
 * Class that defines a text page part object to add to a page
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_intro_text_page_parts")
 */
class IntroTextPagePart extends AbstractPagePart
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
     *
     * @return TextPagePart
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "IntroTextPagePart " . $this->getContent();
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:IntroTextPagePart:view.html.twig";
    }

    /**
     * @return IntroTextPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new IntroTextPagePartAdminType();
    }
}
