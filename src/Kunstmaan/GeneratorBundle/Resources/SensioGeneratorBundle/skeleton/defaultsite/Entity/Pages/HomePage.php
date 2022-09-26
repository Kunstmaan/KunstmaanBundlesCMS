<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\HomePageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HomePageInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

{% if canUseEntityAttributes %}
#[ORM\Entity()]
#[ORM\Table(name: '{{ prefix }}home_pages')]
{% else %}
/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}home_pages")
 */
{% endif %}
class HomePage extends AbstractPage implements HasPageTemplateInterface, SearchTypeInterface, HomePageInterface
{
    public function getDefaultAdminType(): string
    {
        return HomePageAdminType::class;
    }

    public function getPossibleChildTypes(): array
    {
        return [
            [
                'name' => 'ContentPage',
                'class' => '{{ namespace }}\Entity\Pages\ContentPage',
            ],
{% if demosite %}
            [
                'name' => 'FormPage',
                'class' => '{{ namespace }}\Entity\Pages\FormPage',
            ],
{% endif %}
            [
                'name' => 'BehatTestPage',
                'class' => '{{ namespace }}\Entity\Pages\BehatTestPage',
            ],
        ];
    }

    public function getPagePartAdminConfigurations(): array
    {
{% if demosite %}
        return [
            'header',
            'section1',
            'section2',
            'section3',
            'section4',
            'section5',
        ];
{% else %}
	    return ['main'];
{% endif %}
    }

    public function getPageTemplates(): array
    {
        return ['homepage'];
    }

    public function getDefaultView(): string
    {
        return 'Pages/HomePage/view.html.twig';
    }

    public function getSearchType(): string
    {
        return 'Home';
    }
}
