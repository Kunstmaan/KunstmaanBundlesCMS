<?php

namespace Kunstmaan\PagePartBundle\Form;

use Kunstmaan\AdminBundle\Form\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TextPagePartAdminType
 */
class TextPagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', WysiwygType::class, array(
            'label' => 'pagepart.text.content',
            'required' => false,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_pagepartbundle_textpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\TextPagePart',
        ));
    }
}
