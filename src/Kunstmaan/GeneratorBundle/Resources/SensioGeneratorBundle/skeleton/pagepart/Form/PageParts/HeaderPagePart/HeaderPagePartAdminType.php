<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\{{ pagepart }};
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ pagepart }}AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $names = {{ pagepart }}::$supportedHeaders;
        array_walk($names, function (&$item) { $item = 'Header '.$item; });

        $builder->add('niv', ChoiceType::class, [
            'label' => 'pagepart.header.type',
            'choices' => array_combine($names, {{ pagepart }}::$supportedHeaders),
            'required' => true,
        ]);
        $builder->add('title', TextType::class, [
            'label' => 'pagepart.header.title',
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
