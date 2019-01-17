<?php

namespace {{ namespace }}\Form\PageParts;

use {{ pagepart_class_full }};
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HighlightPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('text', WysiwygType::class, [
                'required' => true,
            ])
            ->add('linkUrl', URLChooserType::class, [
                'required' => false,
            ])
            ->add('linkText', TextType::class, [
                'required' => false,
            ])
            ->add('linkNewWindow', CheckboxType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => {{ pagepart_class }}::class,
        ]);
    }
}
