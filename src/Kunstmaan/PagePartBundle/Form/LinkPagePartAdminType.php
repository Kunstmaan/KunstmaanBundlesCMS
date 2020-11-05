<?php

namespace Kunstmaan\PagePartBundle\Form;

use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * LinkPagePartAdminType
 */
class LinkPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', URLChooserType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('openinnewwindow', CheckboxType::class, [
                'label' => 'pagepart.link.openinnewwindow',
                'required' => false,
            ])
            ->add('text', TextType::class, [
                'label' => 'pagepart.link.text',
                'required' => false,
            ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_pagepartbundle_linkpageparttype';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\LinkPagePart',
        ]);
    }
}
