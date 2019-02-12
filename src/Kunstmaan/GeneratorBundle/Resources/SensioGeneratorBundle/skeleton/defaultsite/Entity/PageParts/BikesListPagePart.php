<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;

/**
 * BikesListPagePart
 *
 * @ORM\Table(name="{{ prefix }}bikes_list_page_parts")
 * @ORM\Entity
 */
class BikesListPagePart extends AbstractPagePart
{
    /**
     * Get the twig view.
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}PageParts/BikesListPagePart{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \{{ namespace }}\Form\PageParts\BikesListPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return \{{ namespace }}\Form\PageParts\BikesListPagePartAdminType::class;
    }
}
