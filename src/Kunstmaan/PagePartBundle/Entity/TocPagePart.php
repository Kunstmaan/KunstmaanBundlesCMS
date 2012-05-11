<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\TocPagePartAdminType;

/**
 * TocPagePart
 * 
 * @ORM\Entity
 * @ORM\Table(name="tocpagepart")
 */
class TocPagePart extends AbstractPagePart
{

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "TocPagePart";
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:TocPagePart:view.html.twig";
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
        return new TocPagePartAdminType();
    }
}
