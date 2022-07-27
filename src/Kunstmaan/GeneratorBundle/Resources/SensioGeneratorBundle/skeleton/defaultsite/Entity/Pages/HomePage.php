<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HomePageInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\Form\AbstractType;
use {{ namespace }}\Form\Pages\HomePageAdminType;

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
                'name'  => 'ContentPage',
                'class' => '{{ namespace }}\Entity\Pages\ContentPage'
            ],
{% if demosite %}
            [
                'name'  => 'FormPage',
                'class' => '{{ namespace }}\Entity\Pages\FormPage'
            ],
{% endif %}
            [
                'name'  => 'BehatTestPage',
                'class' => '{{ namespace }}\Entity\Pages\BehatTestPage'
            ]
        ];
    }

    public function getPagePartAdminConfigurations(): array
    {
{% if demosite %}
	    return [
		'{% if not isV4 %}{{ bundle.getName() }}:{%endif%}header',
		'{% if not isV4 %}{{ bundle.getName() }}:{%endif%}section1',
		'{% if not isV4 %}{{ bundle.getName() }}:{%endif%}section2',
		'{% if not isV4 %}{{ bundle.getName() }}:{%endif%}section3',
		'{% if not isV4 %}{{ bundle.getName() }}:{%endif%}section4',
		'{% if not isV4 %}{{ bundle.getName() }}:{%endif%}section5'
        ];
{% else %}
	    return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}main'];
{% endif %}
    }

    public function getPageTemplates(): array
    {
    	return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}homepage'];
    }

    public function getDefaultView(): string
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/HomePage{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }

    public function getSearchType(): string
    {
	    return 'Home';
    }
}
