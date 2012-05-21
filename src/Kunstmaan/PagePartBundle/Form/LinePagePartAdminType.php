<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * LinePagePartAdminType
 */
class LinePagePartAdminType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kunstmaan_pagepartbundle_linepageparttype';
    }
}
