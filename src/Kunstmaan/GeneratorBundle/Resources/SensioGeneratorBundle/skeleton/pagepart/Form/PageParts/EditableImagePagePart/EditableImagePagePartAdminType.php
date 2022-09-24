<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\{{ pagepart }};
use Kunstmaan\MediaBundle\Form\EditableMediaWrapperAdminType;
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
        $builder
            ->add('mediaWrapper', EditableMediaWrapperAdminType::class, [
                'required' => true,
            ])
            ->add('caption', TextType::class, [
                'required' => false,
            ])
            ->add('altText', TextType::class, [
                'required' => false,
                'label' => 'mediapagepart.image.alttext',
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => {{ pagepart }}::class,
        ]);
    }
}
