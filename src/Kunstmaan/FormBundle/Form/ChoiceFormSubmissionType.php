<?php

namespace Kunstmaan\FormBundle\Form;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

/**
 * ChoiceFormSubmissionType
 */
class ChoiceFormSubmissionType extends AbstractType
{

    private $label;
    private $expanded;
    private $multiple;
    private $choices;

    /**
     * @param string  $label    The label
     * @param boolean $expanded Expanded or not
     * @param boolean $multiple Multiple or not
     * @param arra    $choices  The choices array
     */
    public function __construct($label, $expanded, $multiple, $choices)
    {
        $this->label = $label;
        $this->expanded = $expanded;
        $this->multiple = $multiple;
        $this->choices = $choices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
                ->add('value', 'choice',
                    array('data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField', 'label' => $this->label, 'expanded' => $this->expanded,
                                'multiple' => $this->multiple, 'choices' => $this->choices));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_choiceformsubmissiontype';
    }
}

