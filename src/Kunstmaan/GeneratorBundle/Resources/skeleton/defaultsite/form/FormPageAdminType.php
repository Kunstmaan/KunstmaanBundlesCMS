<?php

namespace {{ namespace }}\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FormPageAdminType extends AbstractType
{
    /**
     * @param FormBuilderInterface  $builder The builder
     * @param array                 $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('thanks', 'textarea', array('data_class' => '{{ namespace }}\Entity\FormPage', 'required' => false, 'attr' => array('class' => 'rich_editor')));
    }

    /**
     * @assert () == 'formpage'
     *
     * @return string
     */
    public function getName()
    {
        return 'formpage';
    }
}