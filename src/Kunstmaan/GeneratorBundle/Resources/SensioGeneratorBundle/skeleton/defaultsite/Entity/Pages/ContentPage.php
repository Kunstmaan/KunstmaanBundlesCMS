<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\ContentPageAdminType;
use {{ namespace }}\PagePartAdmin\BannerPagePartAdminConfigurator;
use {{ namespace }}\PagePartAdmin\ContentPagePagePartAdminConfigurator;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Kunstmaan\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;
use Symfony\Component\Form\AbstractType;

/**
 * ContentPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}content_pages")
 */
class ContentPage extends AbstractPage  implements HasPageTemplateInterface
{

    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new ContentPageAdminType();
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array (
            array(
                'name' => 'ContentPage',
                'class'=> "{{ namespace }}\Entity\Pages\ContentPage"
            ),
            array(
                'name' => 'FormPage',
                'class'=> "{{ namespace }}\Entity\Pages\FormPage"
            )
        );
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array("{{ bundle.getName() }}:main", "{{ bundle.getName() }}:banners", "{{ bundle.getName() }}:footer");
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return array("{{ bundle.getName() }}:contentpage", "{{ bundle.getName() }}:contentpage-singlecolumn");
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "{{ bundle.getName() }}:Pages\ContentPage:view.html.twig";
    }
}
