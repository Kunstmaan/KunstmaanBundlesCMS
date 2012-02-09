<?php

namespace Kunstmaan\FormBundle\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ChoicePagePartAdminType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('label', null, array('required' => false))
            ->add('required', 'checkbox', array('required' => false))
            ->add('errormessage_required', 'text', array('required' => false))
            ->add('expanded', 'checkbox', array('required' => false))
            ->add('multiple', 'checkbox', array('required' => false))
            ->add('choices', 'textarea', array('required' => true))
        ;
    }

    public function getName()
    {
        return 'kunstmaan_formbundle_singlelinetextpageparttype';
    }
}

?>