<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}search_page")
 */
class SearchPage extends AbstractSearchPage
{
    public function getDefaultView(): string
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/SearchPage{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }
}
