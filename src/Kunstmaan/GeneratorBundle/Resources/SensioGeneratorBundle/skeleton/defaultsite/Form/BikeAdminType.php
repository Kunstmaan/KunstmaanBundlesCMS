<?php

namespace {{ namespace }}\Form;

use {{ namespace }}\Entity\Bike;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BikeAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('type', ChoiceType::class, [
            'choices' => array_combine(Bike::$types, Bike::$types),
            'placeholder' => false,
            'required' => true,
        ]);
        $builder->add('brand', TextType::class, [
            'required' => true,
        ]);
        $builder->add('model', TextType::class, [
            'required' => true,
        ]);
        $builder->add('price', MoneyType::class, [
            'required' => true,
        ]);
    }
}
