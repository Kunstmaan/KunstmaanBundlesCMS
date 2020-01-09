<?php

namespace {{ namespace }}\Form;

use {{ namespace }}\Entity\GalleryRow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GalleryRowAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mediaItems', CollectionType::class, [
                'entry_type' => GalleryRowMediaItemAdminType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => [
                    'nested_form' => true,
                    'nested_sortable' => true,
                    'nested_form_min' => 1,
                    'nested_form_max' => 3,
                ],
            ])
            ->add('weight', HiddenType::class, [
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GalleryRow::class,
        ]);
    }
}
