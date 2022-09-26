<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\BehatTestPageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

{% if canUseEntityAttributes %}
#[ORM\Entity()]
#[ORM\Table(name: '{{ prefix }}behat_test_pages')]
{% else %}
/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}behat_test_pages")
 */
{% endif %}
class BehatTestPage extends AbstractPage implements HasPageTemplateInterface
{
    public function getDefaultAdminType(): string
    {
        return BehatTestPageAdminType::class;
    }

    public function getPossibleChildTypes(): array
    {
        return [
            [
                'name' => 'HomePage',
                'class' => '{{ namespace }}\Entity\Pages\HomePage',
            ],
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
        ];
    }

    public function getPagePartAdminConfigurations(): array
    {
        return [];
    }

    public function getPageTemplates(): array
    {
        return ['behat-test-page'];
    }

    public function getDefaultView(): string
    {
        return '';
    }
}
