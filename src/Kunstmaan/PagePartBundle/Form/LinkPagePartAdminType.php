<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * LinkPagePartAdminType
 */
class LinkPagePartAdminType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('url', 'urlchooser', array( 'required' => false))
            ->add('openinnewwindow', 'checkbox', array('required' => false))
            ->add('text', null, array('required' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\LinkPagePart',
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kunstmaan_pagepartbundle_linkpageparttype';
    }
}