<?php

namespace {{ namespace }}\Form\PageParts;

use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * {{ pagepart }}AdminType
 */
class {{ pagepart }}AdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('media', MediaType::class, array(
            'label' => 'mediapagepart.image.choosefile',
            'mediatype' => 'image',
            'required' => true
        ));
        $builder->add('caption', TextType::class, array(
            'required' => false
        ));
        $builder->add('altText', TextType::class, array(
            'required' => false,
            'label' => 'mediapagepart.image.alttext'
        ));
        $builder->add('link', URLChooserType::class, array(
            'required' => false,
            'label' => 'mediapagepart.image.link'
        ));
        $builder->add('openInNewWindow', CheckboxType::class, array(
            'required' => false,
            'label' => 'mediapagepart.image.openinnewwindow'
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
            'data_class' => '\{{ namespace }}\Entity\PageParts\{{ pagepart }}',
        ));
    }
}
