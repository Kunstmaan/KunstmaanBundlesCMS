<?php

namespace Kunstmaan\FormBundle\Tests\Entity;

use Kunstmaan\FormBundle\Entity\AbstractFormPage;

class FormPage extends AbstractFormPage
{
    public function getPossibleChildTypes()
    {
        return null;
    }

    public function getPagePartAdminConfigurations()
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

    public function getDefaultView()
    {
        return 'some.twig';
    }
}