<?php

namespace {{ namespace }}\Form;

use {{ namespace }}\Entity\UspItem;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UspItemAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('icon', MediaType::class, [
                'mediatype' => 'image',
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'required' => true,
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => 4,
                    'cols' => 600,
                ],
                'required' => false,
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
            ->add('weight', HiddenType::class, [
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UspItem::class,
        ]);
    }
}
