<?php

namespace {{ namespace }}\Entity\Pages;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\PagePartBundle\Helper\HasPageTemplateInterface;
use Symfony\Component\Form\AbstractType;
use {{ namespace }}\Form\Pages\FormPageAdminType;

/**
 * FormPage
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}form_pages")
 */
class FormPage extends AbstractFormPage implements HasPageTemplateInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return FormPageAdminType::class;
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
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

    /**
     * @return string[]
     */
    public function getPagePartAdminConfigurations()
    {
        return ['{{ bundle.getName() }}:form'];
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTemplates()
    {
        return ['{{ bundle.getName() }}:formpage'];
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle.getName() }}:Pages\FormPage:view.html.twig';
    }
}
