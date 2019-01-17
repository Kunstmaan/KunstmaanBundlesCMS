<?php

namespace {{ namespace }}\Form\PageParts;

use {{ pagepart_class_full }};
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VideoPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('video', MediaType::class, [
                'mediatype' => 'video',
                'required' => true,
            ])
            ->add('thumbnail', MediaType::class, [
                'mediatype' => 'image',
                'required' => false,
            ])
            ->add('caption', TextType::class, [
                'required' => false,
            ])
            ->add('width', ChoiceType::class, [
                'choices' => {{ pagepart_class }}::VIDEO_WIDTH,
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
