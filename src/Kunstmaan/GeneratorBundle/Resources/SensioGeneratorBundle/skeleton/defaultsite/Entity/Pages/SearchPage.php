<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}search_pages")
 */
class SearchPage extends AbstractSearchPage implements HasPageTemplateInterface
{
    /**
     * return string
     */
    public function getDefaultView()
    {
        return "{{ bundle.getName() }}:Pages:SearchPage/view.html.twig";
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array("{{ bundle.getName() }}:main");
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return array("{{ bundle.getName() }}:searchpage");
    }
}
