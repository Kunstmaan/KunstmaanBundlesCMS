<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\TextPagePartAdminType;

/**
 * Class that defines a text page part object to add to a page
 *
 * @ORM\Entity
 * @ORM\Table(name="textpagepart")
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
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "TextPagePart " . $this->getContent();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:TextPagePart:view.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new TextPagePartAdminType();
    }
}
