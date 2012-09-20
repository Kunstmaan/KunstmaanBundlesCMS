<?php

namespace Kunstmaan\PagePartBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\ToTopPagePartAdminType;

/**
 * ToTopPagePart
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_to_top_page_parts")
 */
class ToTopPagePart extends AbstractPagePart
{

    /**
     * @assert () == 'ToTopPagePart'
     *
     * @return string
     */
    public function __toString()
    {
        return "ToTopPagePart";
    }

    /**
     * @assert () == 'KunstmaanPagePartBundle:ToTopPagePart:view.html.twig'
     *
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanPagePartBundle:ToTopPagePart:view.html.twig";
    }

    /**
     * @assert () == 'KunstmaanPagePartBundle:ToTopPagePart:view.html.twig'
     *
     * @return string
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * @return ToTopPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new ToTopPagePartAdminType();
    }
}
