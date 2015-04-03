<?php

namespace {{ namespace }}\Form;

use {{ namespace }}\Entity\Bike;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

class BikeAdminType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
	$builder->add('type', 'choice', array(
	    'choices' => array_combine(Bike::$types, Bike::$types),
	    'empty_value' => false,
	    'required' => true
	));
	$builder->add('brand', 'text', array(
	    'required' => true
	));
	$builder->add('model', 'text', array(
	    'required' => true
	));
	$builder->add('price', 'money', array(
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
	return 'bike';
    }
}
