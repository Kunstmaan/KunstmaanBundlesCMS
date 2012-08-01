<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

class TextPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array('required' => false, 'attr' => array( 'rows'=>32, 'cols'=>600, 'class' => 'rich_editor' )))
        ;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\TextPagePart',
        );
    }

    public function getName()
    {
        return 'kunstmaan_pagepartbundle_textpageparttype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
                'data_class' => 'Kunstmaan\PagePartBundle\Entity\TextPagePart',
        );
    }
}
