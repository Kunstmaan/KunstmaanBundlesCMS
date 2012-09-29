<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\LinePagePartAdminType;

/**
 * LinePagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_line_page_parts")
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
     * @return LinePagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new LinePagePartAdminType();
    }
}
