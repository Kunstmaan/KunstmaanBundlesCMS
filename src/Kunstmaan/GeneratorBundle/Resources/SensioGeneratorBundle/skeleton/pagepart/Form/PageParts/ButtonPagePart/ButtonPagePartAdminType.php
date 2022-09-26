<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\{{ pagepart }};
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ pagepart }}AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('linkUrl', URLChooserType::class, [
            'required' => true,
        ]);
        $builder->add('linkText', TextType::class, [
            'required' => true,
        ]);
        $builder->add('linkNewWindow', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('type', ChoiceType::class, [
            'choices' => array_combine({{ pagepart }}::$types, {{ pagepart }}::$types),
            'placeholder' => false,
            'required' => true,
        ]);
        $builder->add('size', ChoiceType::class, [
            'choices' => array_combine({{ pagepart }}::$sizes, {{ pagepart }}::$sizes),
            'placeholder' => false,
            'required' => true,
        ]);
        $builder->add('position', ChoiceType::class, [
            'choices' => array_combine({{ pagepart }}::$positions, {{ pagepart }}::$positions),
            'placeholder' => false,
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => {{ pagepart }}::class,
        ]);
    }
}
