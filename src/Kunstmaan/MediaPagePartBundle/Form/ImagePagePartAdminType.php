<?php

namespace Kunstmaan\MediaPagePartBundle\Form;

use Kunstmaan\MediaBundle\Form\Type\MediaType;
use Kunstmaan\NodeBundle\Form\Type\URLChooserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ImagePagePartAdminType
 */
class ImagePagePartAdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('media', MediaType::class, array(
            'label' => 'mediapagepart.image.choosefile',
        ));
        $builder->add('alttext', TextType::class, array(
            'required' => false,
            'label' => 'mediapagepart.image.alttext',
        ));
        $builder->add('link', URLChooserType::class, array(
            'required' => false,
            'label' => 'mediapagepart.image.link',
        ));
        $builder->add('openinnewwindow', CheckboxType::class, array(
            'required' => false,
            'label' => 'mediapagepart.image.openinnewwindow',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_mediabundle_imagepageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'Kunstmaan\MediaPagePartBundle\Entity\ImagePagePart',
        ));
    }
}
