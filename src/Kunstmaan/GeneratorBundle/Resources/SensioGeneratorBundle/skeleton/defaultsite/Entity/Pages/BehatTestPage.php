<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;

use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\Form\AbstractType;
use {{ namespace }}\Form\Pages\BehatTestPageAdminType;

/**
 * BehatTestPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}behat_test_pages")
 */
class BehatTestPage extends AbstractPage implements HasPageTemplateInterface
{

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return BehatTestPageAdminType::class;
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array(
            array(
                'name'  => 'HomePage',
                'class' => '{{ namespace }}\Entity\Pages\HomePage'
            ),
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
        );
    }

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return array();
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return array('{% if not isV4 %}{{ bundle.getName() }}:{%endif%}behat-test-page');
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '';
    }
}
