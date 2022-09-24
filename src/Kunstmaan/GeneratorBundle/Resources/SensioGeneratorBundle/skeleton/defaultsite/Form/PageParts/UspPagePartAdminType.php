<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\UspPagePart;
use {{ namespace }}\Form\UspItemAdminType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UspPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('items', CollectionType::class, [
            'entry_type' => UspItemAdminType::class,
            'allow_add' => true,
            'allow_delete' => true,
            'by_reference' => false,
            'attr' => [
                'nested_form' => true,
                'nested_sortable' => true,
                'nested_form_min' => 1,
                'nested_form_max' => 3,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UspPagePart::class,
        ]);
    }
}
