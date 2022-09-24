<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\{{ pagepart }};
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ pagepart }}AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('media', MediaType::class, [
            'label' => 'mediapagepart.image.choosefile',
            'mediatype' => 'image',
            'required' => true,
        ]);
        $builder->add('caption', TextType::class, [
            'required' => false,
        ]);
        $builder->add('altText', TextType::class, [
            'required' => false,
            'label' => 'mediapagepart.image.alttext',
        ]);
        $builder->add('link', URLChooserType::class, [
            'required' => false,
            'label' => 'mediapagepart.image.link',
        ]);
        $builder->add('openInNewWindow', CheckboxType::class, [
            'required' => false,
            'label' => 'mediapagepart.image.openinnewwindow',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => {{ pagepart }}::class,
        ]);
    }
}
