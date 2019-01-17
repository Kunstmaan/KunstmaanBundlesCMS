<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ admin_type_full }};
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="{{ table_name }}")
 * @ORM\Entity
 */
class DividerPagePart extends AbstractPagePart
{
    public function getDefaultView(): string
    {
        return 'pageparts/divider_pagepart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return {{ admin_type_class }}::class;
    }
}
