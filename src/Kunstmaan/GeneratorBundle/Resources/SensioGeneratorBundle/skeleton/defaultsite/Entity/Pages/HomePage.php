<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HomePageInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchTypeInterface;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\Form\AbstractType;
use {{ namespace }}\Form\Pages\HomePageAdminType;

/**
 * HomePage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}home_pages")
 */
class HomePage extends AbstractPage implements HasPageTemplateInterface, SearchTypeInterface, HomePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return HomePageAdminType::class;
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array(
            array(
                'name'  => 'ContentPage',
                'class' => '{{ namespace }}\Entity\Pages\ContentPage'
            ),
{% if demosite %}
            array(
                'name'  => 'FormPage',
                'class' => '{{ namespace }}\Entity\Pages\FormPage'
            ),
{% endif %}
            array(
                'name'  => 'BehatTestPage',
                'class' => '{{ namespace }}\Entity\Pages\BehatTestPage'
            )
        );
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
{% if demosite %}
	    return array(
		'{{ bundle.getName() }}:header',
		'{{ bundle.getName() }}:section1',
		'{{ bundle.getName() }}:section2',
		'{{ bundle.getName() }}:section3',
		'{{ bundle.getName() }}:section4',
		'{{ bundle.getName() }}:section5'
	    );
{% else %}
	    return array('{{ bundle.getName() }}:main');
{% endif %}
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
    	return array('{{ bundle.getName() }}:homepage');
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle.getName() }}:Pages\HomePage:view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchType()
    {
	    return 'Home';
    }
}
