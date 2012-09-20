<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * This class represents the type for the ChoicePagePart
 */
class ChoicePagePartAdminType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', null, array('required' => false))
            ->add('required', 'checkbox', array('required' => false))
            ->add('errormessage_required', 'text', array('required' => false))
            ->add('expanded', 'checkbox', array('required' => false))
            ->add('multiple', 'checkbox', array('required' => false))
            ->add('choices', 'textarea', array('required' => false))
            ->add('empty_value', 'text', array('required' => false));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_choicepageparttype';
    }
}
