<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\RawHTMLPagePartAdminType;

/**
 * Class that defines a raw html page part object to add to a page
 *
 * @ORM\Entity
 * @ORM\Table(name="rawhtmlpagepart")
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
        return "KunstmaanPagePartBundle:RawHTMLPagePart:view.html.twig";
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
        return new RawHTMLPagePartAdminType();
    }
}
