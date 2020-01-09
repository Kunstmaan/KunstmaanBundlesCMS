<?php

namespace {{ namespace }}\Form\PageParts;

use {{ pagepart_class_full }};
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MediaWithContentPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('imagePosition', ChoiceType::class, [
                'choices' => {{ pagepart_class }}::IMAGE_POSITION,
                'required' => true,
            ])
            ->add('image', MediaType::class, [
                'mediatype' => 'image',
                'required' => true,
            ])
            ->add('imageAltText', TextType::class, [
                'required' => false,
            ])
            ->add('contentAlignment', ChoiceType::class, [
                'choices' => {{ pagepart_class }}::CONTENT_ALIGNMENT,
                'required' => true,
            ])
            ->add('title', TextType::class, [
                'label' => 'pagepart.header.title',
                'required' => false,
            ])
            ->add('text', WysiwygType::class, [
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => {{ pagepart_class }}::class,
        ]);
    }
}
