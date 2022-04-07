<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\Form\AbstractType;
use {{ namespace }}\Form\Pages\FormPageAdminType;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}form_pages")
 */
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
                'name'  => 'ContentPage',
                'class' => '{{ namespace }}\Entity\Pages\ContentPage'
            ],
            [
                'name'  => 'FormPage',
                'class' => '{{ namespace }}\Entity\Pages\FormPage'
            ]
        ];
    }

    public function getPagePartAdminConfigurations(): array
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}form'];
    }

    public function getPageTemplates(): array
    {
        return ['{% if not isV4 %}{{ bundle.getName() }}:{%endif%}formpage'];
    }

    public function getDefaultView(): string
    {
        return '{% if not isV4 %}{{ bundle.getName() }}:{%endif%}Pages/FormPage{% if not isV4 %}:{% else %}/{% endif %}view.html.twig';
    }
}
