<?php

namespace Kunstmaan\PagePartBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\AbstractType;

/**
 * IntroTextPagePartAdminType
 */
class IntroTextPagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', 'textarea', array('label' => 'pagepart.text.content', 'required' => false, 'attr' => array('rows' => 32, 'cols' => 600, 'class' => 'rich_editor')));
    }

    /**
     * @assert () == 'kunstmaan_pagepartbundle_introtextpageparttype'
     *
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_pagepartbundle_introtextpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\IntroTextPagePart',
        ));
    }
}
