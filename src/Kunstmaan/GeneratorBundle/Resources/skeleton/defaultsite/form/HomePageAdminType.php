<?php

namespace {{ namespace }}\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class HomePageAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface  $builder The builder
     * @param array                 $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
    }

    /**
     * @assert () == 'homepage'
     *
     * @return string
     */
    public function getName()
    {
        return 'homepage';
    }
}