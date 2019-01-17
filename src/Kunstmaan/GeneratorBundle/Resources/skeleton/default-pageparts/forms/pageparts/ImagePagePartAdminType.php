<?php

namespace {{ namespace }}\Form\PageParts;

use {{ pagepart_class_full }};
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImagePagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', MediaType::class, [
                'label' => 'mediapagepart.image.choosefile',
                'mediatype' => 'image',
                'required' => true,
            ])
            ->add('caption', TextType::class, [
                'required' => false,
            ])
            ->add('altText', TextType::class, [
                'required' => false,
                'label' => 'mediapagepart.image.alttext',
            ])
            ->add('alignment', ChoiceType::class, [
                'choices' => {{ pagepart_class }}::IMAGE_ALIGNMENT,
                'required' => true,
            ])
            ->add('width', ChoiceType::class, [
                'choices' => {{ pagepart_class }}::IMAGE_WIDTH,
                'required' => true,
            ])
            ->add('link', URLChooserType::class, [
                'required' => false,
                'label' => 'mediapagepart.image.link',
            ])
            ->add('openInNewWindow', CheckboxType::class, [
                'required' => false,
                'label' => 'mediapagepart.image.openinnewwindow',
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
