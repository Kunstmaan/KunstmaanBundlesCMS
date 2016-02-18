<?php

namespace {{ namespace }}\Form;

use {{ namespace }}\Entity\Bike;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

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
	$builder->add('type', ChoiceType::class, array(
	    'choices' => array_combine(Bike::$types, Bike::$types),
	    'placeholder' => false,
	    'required' => true,
	));
	$builder->add('brand', TextType::class, array(
	    'required' => true
	));
	$builder->add('model', TextType::class, array(
	    'required' => true
	));
	$builder->add('price', MoneyType::class, array(
	    'required' => true
	));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
	return 'bike';
    }
}
