<?php

namespace Kunstmaan\FormBundle\Form;
use Symfony\Component\Form\FormBuilderInterface;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Symfony\Component\Form\AbstractType;

/**
 * ChoiceFormSubmissionType
 */
class ChoiceFormSubmissionType extends AbstractType
{

    private $label;
    private $expanded;
    private $multiple;
    private $choices;
	private $empty_value;

    /**
     * @param string  $label    The label
     * @param boolean $expanded Expanded or not
     * @param boolean $multiple Multiple or not
     * @param array   $choices  The choices array
     */
    public function __construct($label, $expanded, $multiple, $choices, $empty_value = null)
    {
        $this->label = $label;
        $this->expanded = $expanded;
        $this->multiple = $multiple;
        $this->choices = $choices;
		$this->empty_value = $empty_value;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('value', 'choice', array(
			'label' => $this->label,
			'expanded' => $this->expanded,
			'multiple' => $this->multiple,
			'choices' => $this->choices,
			'empty_value' => $this->empty_value,
			'empty_data' => null
		));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'kunstmaan_formbundle_choiceformsubmissiontype';
    }
}

