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
     * @assert () == 'TocPagePart'
     *
     * @return string
     */
    public function __toString()
    {
        return "TocPagePart";
    }

    /**
     * @assert () == 'KunstmaanPagePartBundle:TocPagePart:view.html.twig'
     *
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:TocPagePart:view.html.twig";
    }

    /**
     * @assert () == 'KunstmaanPagePartBundle:TocPagePart:view.html.twig'
     *
     * @return string
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * @return TocPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new TocPagePartAdminType();
    }
}
