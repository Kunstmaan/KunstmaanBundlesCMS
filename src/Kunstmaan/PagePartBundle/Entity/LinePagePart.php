<?php

namespace Kunstmaan\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\LinePagePartAdminType;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_line_page_parts")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_line_page_parts')]
class LinePagePart extends AbstractPagePart
{
    public function __toString()
    {
        return 'LinePagePart';
    }

    public function getDefaultView()
    {
        return '@KunstmaanPagePart/LinePagePart/view.html.twig';
    }

    public function getDefaultAdminType()
    {
        return LinePagePartAdminType::class;
    }
}
