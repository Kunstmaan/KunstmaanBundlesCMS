<?php

namespace {{ namespace }}\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FormPageAdminType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder->add('title');
        $builder->add('thanks', 'textarea', array('data_class' => '{{ namespace }}\Entity\FormPage', 'required' => false, 'attr' => array('class' => 'rich_editor')));
    }

    public function getName()
    {
        return 'formpage';
    }
}