<?php

namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Symfony\Component\Form\AbstractType;

/**
 * ChoicePagePartAdminType
 */
class ChoicePagePartAdminType extends AbstractType
{
    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_choicepageparttype';
    }
}

