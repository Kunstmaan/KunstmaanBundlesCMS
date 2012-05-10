<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\LinePagePartAdminType;

/**
 * LinePagePart
 * 
 * @ORM\Entity
 * @ORM\Table(name="linepagepart")
 */
class LinePagePart extends AbstractPagePart
{

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return "LinePagePart";
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:LinePagePart:view.html.twig";
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
        return new LinePagePartAdminType();
    }
}
