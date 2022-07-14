<?php

namespace {{ namespace }}\Form\PageParts;

use {{ pagepart_class_full }};
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('linkUrl', URLChooserType::class, [
                'required' => true,
            ])
            ->add('linkText', TextType::class, [
                'required' => true,
            ])
            ->add('linkNewWindow', CheckboxType::class, [
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => array_combine({{ pagepart_class }}::$types, {{ pagepart_class }}::$types),
                'placeholder' => false,
                'required' => true,
            ])
            ->add('size', ChoiceType::class, [
                'choices' => array_combine({{ pagepart_class }}::$sizes, {{ pagepart_class }}::$sizes),
                'placeholder' => false,
                'required' => true,
            ])
            ->add('position', ChoiceType::class, [
                'choices' => array_combine({{ pagepart_class }}::$positions, {{ pagepart_class }}::$positions),
                'placeholder' => false,
                'required' => true,
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
