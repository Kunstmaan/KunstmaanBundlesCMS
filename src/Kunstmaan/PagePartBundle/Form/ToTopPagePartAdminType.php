<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

/**
 * ToTopPagePartAdminType
 */
class ToTopPagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @assert () == 'kunstmaan_pagepartbundle_totoppageparttype'
     *
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_pagepartbundle_totoppageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\ToTopPagePart',
        ));
    }
}
