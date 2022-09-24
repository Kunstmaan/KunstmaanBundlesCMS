<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\{{ pagepart }};
use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ pagepart }}AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('video', MediaType::class, [
            'mediatype' => 'video',
            'required' => true,
        ]);
        $builder->add('thumbnail', MediaType::class, [
            'mediatype' => 'image',
            'required' => false,
        ]);
        $builder->add('caption', TextType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => {{ pagepart }}::class,
        ]);
    }
}
