<?php

namespace {{ namespace }}\Form\PageParts;

use {{ pagepart_class_full }};
use {{ namespace }}\Form\GalleryRowAdminType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('rows', CollectionType::class, [
                'entry_type' => GalleryRowAdminType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => [
                    'nested_form' => true,
                    'nested_sortable' => true,
                    'nested_form_min' => 1,
                ],
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
