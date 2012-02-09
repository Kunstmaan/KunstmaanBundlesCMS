<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SubmitButtonPagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('label', null, array('required' => false))
        ;
    }

    public function getName()
    {
        return 'kunstmaan_formbundle_singlelinetextpageparttype';
    }
}

?>