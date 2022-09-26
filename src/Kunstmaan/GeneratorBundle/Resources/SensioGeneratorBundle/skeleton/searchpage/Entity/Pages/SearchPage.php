<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}search_pages')]
{% else %}
/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}search_pages")
 */
{% endif %}
class SearchPage extends AbstractSearchPage
{
    public function getDefaultView(): string
    {
        return 'Pages/SearchPage/view.html.twig';
    }
}
