<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\ServicePagePart;
use Kunstmaan\AdminBundle\Form\WysiwygType;
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ServicePagePartAdminType extends \Symfony\Component\Form\AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('title', TextType::class, [
            'required' => true,
        ]);
        $builder->add('description', WysiwygType::class, [
            'required' => false,
        ]);
        $builder->add('linkUrl', URLChooserType::class, [
            'required' => false,
        ]);
        $builder->add('linkText', TextType::class, [
            'required' => false,
        ]);
        $builder->add('linkNewWindow', CheckboxType::class, [
            'required' => false,
        ]);
        $builder->add('image', MediaType::class, [
            'mediatype' => 'image',
            'required' => false,
        ]);
        $builder->add('imagePosition', ChoiceType::class, [
            'choices' => array_combine(ServicePagePart::$imagePositions, ServicePagePart::$imagePositions),
            'placeholder' => false,
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ServicePagePart::class,
        ]);
    }
}
