<?php

namespace {{ namespace }}\Entity\PageParts;

use {{ namespace }}\Form\PageParts\BikesListPagePartAdminType;
use Doctrine\ORM\Mapping as ORM;

{% if canUseEntityAttributes %}
#[ORM\Entity()]
#[ORM\Table(name: '{{ prefix }}bikes_list_page_parts')]
{% else %}
/**
 * @ORM\Table(name="{{ prefix }}bikes_list_page_parts")
 * @ORM\Entity
 */
{% endif %}
class BikesListPagePart extends AbstractPagePart
{
    public function getDefaultView(): string
    {
        return 'PageParts/BikesListPagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return BikesListPagePartAdminType::class;
    }
}
