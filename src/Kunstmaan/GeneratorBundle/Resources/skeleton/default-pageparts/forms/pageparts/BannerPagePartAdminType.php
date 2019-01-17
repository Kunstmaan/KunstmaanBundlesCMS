<?php

namespace {{ namespace }}\Form\PageParts;

use {{ pagepart_class_full }};
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BannerPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('text', WysiwygType::class, [
                'required' => false,
            ])
            ->add('backgroundImage', MediaType::class, [
                'mediatype' => 'image',
                'required' => false,
            ])
            ->add('image', MediaType::class, [
                'mediatype' => 'image',
                'required' => false,
            ])
            ->add('buttonText', TextType::class, [
                'required' => false,
            ])
            ->add('buttonLink', URLChooserType::class, [
                'required' => false,
            ])
            ->add('openInNewWindow', CheckboxType::class, [
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
