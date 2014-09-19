<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\ContentPageAdminType;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
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
                'name'  => 'ContentPage',
                'class' => '{{ namespace }}\Entity\Pages\ContentPage'
            ),
{% if demosite %}
            array(
                'name'  => 'SatelliteOverviewPage',
                'class' => '{{ namespace }}\Entity\Pages\SatelliteOverviewPage'
            )
{% endif %}
        );
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array('{{ bundle.getName() }}:main');
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return array('{{ bundle.getName() }}:contentpage');
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle.getName() }}:Pages\ContentPage:view.html.twig';
    }
}
