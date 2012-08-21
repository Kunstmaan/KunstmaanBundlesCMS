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
     * @return string
     */
    public function __toString()
    {
        return "LinePagePart";
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:LinePagePart:view.html.twig";
    }

    /**
     * @return string
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * @return LinePagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new LinePagePartAdminType();
    }
}
