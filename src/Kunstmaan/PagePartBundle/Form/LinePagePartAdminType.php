<?php

namespace Kunstmaan\PagePartBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

/**
 * LinePagePartAdminType
 */
class LinePagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @assert () == 'kunstmaan_pagepartbundle_linepageparttype'
     *
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_pagepartbundle_linepageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                               'data_class' => 'Kunstmaan\PagePartBundle\Entity\LinePagePart',
                               ));
    }
}
