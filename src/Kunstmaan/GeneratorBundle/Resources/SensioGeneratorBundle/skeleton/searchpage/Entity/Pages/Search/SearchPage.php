<?php

namespace {{ namespace }}\Entity\Pages\Search;

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
        return "{{ bundle.getName() }}:Pages\Search\SearchPage:view.html.twig";
    }

}
