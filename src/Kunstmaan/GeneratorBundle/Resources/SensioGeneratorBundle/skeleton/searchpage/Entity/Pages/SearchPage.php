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
    /*
     * return string
     */
    public function getDefaultView()
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/SearchPage:view.html.twig';
    }
}
