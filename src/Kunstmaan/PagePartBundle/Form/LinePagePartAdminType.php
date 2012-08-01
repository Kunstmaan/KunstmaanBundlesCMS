<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Symfony\Component\Form\AbstractType;

/**
 * LinePagePartAdminType
 */
class LinePagePartAdminType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
