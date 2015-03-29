<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\ServicePagePart;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * ServicePagePartAdminType
 */
class ServicePagePartAdminType extends \Symfony\Component\Form\AbstractType
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

	$builder->add('title', 'text', array(
	    'required' => true,
	));
	$builder->add('description', 'textarea', array(
	    'attr' => array('rows' => 10, 'cols' => 600, 'class' => 'js-rich-editor rich-editor', 'height' => 140),
	    'required' => false,
	));
	$builder->add('linkUrl', 'urlchooser', array(
	    'required' => false,
	));
	$builder->add('linkText', 'text', array(
	    'required' => false,
	));
	$builder->add('linkNewWindow', 'checkbox', array(
	    'required' => false,
	));
	$builder->add('image', 'media', array(
	    'pattern' => 'KunstmaanMediaBundle_chooser',
	    'mediatype' => 'image',
	    'required' => false,
	));
	$builder->add('imagePosition', 'choice', array(
	    'choices' => array_combine(ServicePagePart::$imagePositions, ServicePagePart::$imagePositions),
	    'empty_value' => false,
	    'required' => true
	));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
	return 'servicepageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolverInterface $resolver The resolver for the options.
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
	$resolver->setDefaults(array(
	    'data_class' => '\{{ namespace }}\Entity\PageParts\ServicePagePart'
	));
    }
}
