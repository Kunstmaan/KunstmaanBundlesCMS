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
	return '{{ bundle.getName() }}:PageParts:BikesListPagePart/view.html.twig';
    }

    /**
     * Get the admin form type.
     *
     * @return \{{ namespace }}\Form\PageParts\BikesListPagePartAdminType
     */
    public function getDefaultAdminType()
    {
	return new \{{ namespace }}\Form\PageParts\BikesListPagePartAdminType();
    }
}
