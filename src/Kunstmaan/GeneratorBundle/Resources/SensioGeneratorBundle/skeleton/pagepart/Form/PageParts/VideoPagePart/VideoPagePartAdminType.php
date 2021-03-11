<?php

namespace {{ namespace }}\Form\PageParts;

use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class {{ pagepart }}AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('video', MediaType::class, array(
            'mediatype' => 'video',
            'required' => true
        ));
        $builder->add('thumbnail', MediaType::class, array(
            'mediatype' => 'image',
            'required' => false
        ));
        $builder->add('caption', TextType::class, array(
            'required' => false
        ));
    }


    public function getBlockPrefix()
    {
        return '{{ pagepart|lower }}type';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '{{ namespace }}\Entity\PageParts\{{ pagepart }}',
        ));
    }
}
