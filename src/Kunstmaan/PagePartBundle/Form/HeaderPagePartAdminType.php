<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * HeaderPagePartAdminType
 */
class HeaderPagePartAdminType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('niv', 'choice', array(
            'choices'   => array('1' => 'Header 1', '2' => 'Header 2', '3' => 'Header 3', '4' => 'Header 4', '5' => 'Header 5', '6' => 'Header 6' ),
            'required'  => false,
        ));
        $builder
            ->add('title', null, array('required' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kunstmaan_pagepartbundle_headerpageparttype';
    }
}
