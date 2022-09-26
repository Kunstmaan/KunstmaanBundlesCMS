<?php

namespace {{ namespace }}\Entity\Pages;

use {{ namespace }}\Form\Pages\FormPageAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;

{% if canUseEntityAttributes %}
#[ORM\Entity()]
#[ORM\Table(name: '{{ prefix }}form_pages')]
{% else %}
/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}form_pages")
 */
{% endif %}
class FormPage extends AbstractFormPage implements HasPageTemplateInterface
{
    public function getDefaultAdminType(): string
    {
        return FormPageAdminType::class;
    }

    public function getPossibleChildTypes(): array
    {
        return [
            [
                'name' => 'ContentPage',
                'class' => '{{ namespace }}\Entity\Pages\ContentPage',
            ],
            [
                'name' => 'FormPage',
                'class' => '{{ namespace }}\Entity\Pages\FormPage',
            ],
        ];
    }

    public function getPagePartAdminConfigurations(): array
    {
        return ['form'];
    }

    public function getPageTemplates(): array
    {
        return ['formpage'];
    }

    public function getDefaultView(): string
    {
        return 'Pages/FormPage/view.html.twig';
    }
}
