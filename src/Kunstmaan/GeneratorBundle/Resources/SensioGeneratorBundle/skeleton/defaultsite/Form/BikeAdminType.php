<?php

namespace {{ namespace }}\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use {{ namespace }}\Entity\Bike;

class BikeAdminType extends AbstractType
{

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


    public function getBlockPrefix()
    {
	return 'bike';
    }
}
