<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\ToTopPagePartAdminType;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_to_top_page_parts")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_to_top_page_parts')]
class ToTopPagePart extends AbstractPagePart
{
    /**
     * @return string
     */
    public function __toString()
    {
        return 'ToTopPagePart';
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '@KunstmaanPagePart/ToTopPagePart/view.html.twig';
    }

    public function getDefaultAdminType()
    {
        return ToTopPagePartAdminType::class;
    }
}
