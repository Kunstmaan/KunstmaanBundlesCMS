<?php

namespace {{ namespace }}\Form\PageParts;

use {{ namespace }}\Entity\PageParts\{{ pagepart }};
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
     * @param array $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	parent::buildForm($builder, $options);

	$builder->add('linkUrl', 'urlchooser', array(
	    'required' => true
	));
	$builder->add('linkText', 'text', array(
	    'required' => true
	));
	$builder->add('linkNewWindow', 'checkbox', array(
	    'required' => false,
	));
	$builder->add('type', 'choice', array(
	    'choices' => array_combine({{ pagepart }}::$types, {{ pagepart }}::$types),
	    'empty_value' => false,
	    'required' => true
	));
	$builder->add('size', 'choice', array(
	    'choices' => array_combine({{ pagepart }}::$sizes, {{ pagepart }}::$sizes),
	    'empty_value' => false,
	    'required' => true
	));
	$builder->add('position', 'choice', array(
	    'choices' => array_combine({{ pagepart }}::$positions, {{ pagepart }}::$positions),
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
	    'data_class' => '\{{ namespace }}\Entity\PageParts\{{ pagepart }}'
	));
    }
}
