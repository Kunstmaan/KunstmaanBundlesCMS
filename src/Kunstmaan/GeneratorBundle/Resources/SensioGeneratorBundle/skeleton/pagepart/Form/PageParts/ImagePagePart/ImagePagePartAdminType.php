<?php

namespace {{ namespace }}\Form\PageParts;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * {{ pagepart }}AdminType
 */
class {{ pagepart }}AdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('media', 'media', array(
            'pattern' => 'KunstmaanMediaBundle_chooser',
            'label' => 'mediapagepart.image.choosefile',
            'mediatype' => 'image',
            'required' => true
        ));
        $builder->add('caption', 'text', array(
            'required' => false
        ));
        $builder->add('altText', 'text', array(
            'required' => false,
            'label' => 'mediapagepart.image.alttext'
        ));
        $builder->add('link', 'urlchooser', array(
            'required' => false,
            'label' => 'mediapagepart.image.link'
        ));
        $builder->add('openInNewWindow', 'checkbox', array(
            'required' => false,
            'label' => 'mediapagepart.image.openinnewwindow'
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return '{{ pagepart|lower }}type';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\{{ namespace }}\Entity\PageParts\{{ pagepart }}',
        ));
    }
}
