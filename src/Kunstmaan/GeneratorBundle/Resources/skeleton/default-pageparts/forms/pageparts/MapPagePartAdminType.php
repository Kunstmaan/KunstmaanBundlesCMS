<?php

namespace {{ namespace }}\Form\PageParts;

use {{ pagepart_class_full }};
use {{ namespace }}\Form\MapItemAdminType;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MapPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', WysiwygType::class)
            ->add('items', CollectionType::class, [
            'entry_type' => MapItemAdminType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'attr' => [
                'nested_form' => true,
                'nested_sortable' => false,
                'nested_form_min' => 1,
                'nested_form_max' => 3,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => {{ pagepart_class }}::class,
        ]);
    }
}
