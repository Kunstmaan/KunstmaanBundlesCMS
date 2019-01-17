<?php

namespace {{ namespace }}\Form\PageParts;

use {{ pagepart_class_full }};
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HeaderPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $names = {{ pagepart_class }}::$supportedHeaders;
        array_walk($names, function (&$item) { $item = 'Header '.$item; });

        $builder
            ->add('niv', ChoiceType::class, [
                'label' => 'pagepart.header.type',
                'choices' => array_combine($names, {{ pagepart_class }}::$supportedHeaders),
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'label' => 'pagepart.header.title',
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
