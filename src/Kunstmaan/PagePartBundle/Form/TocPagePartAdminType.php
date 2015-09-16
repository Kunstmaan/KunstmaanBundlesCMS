<?php

namespace Kunstmaan\PagePartBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;

/**
 * TocPagePartAdminType
 */
class TocPagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @assert () == 'kunstmaan_pagepartbundle_tocpageparttype'
     *
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_pagepartbundle_tocpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\TocPagePart',
        ));
    }
}
