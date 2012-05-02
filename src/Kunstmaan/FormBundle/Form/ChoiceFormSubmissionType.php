<?php

namespace Kunstmaan\FormBundle\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class ChoiceFormSubmissionType extends AbstractType
{

	private $label;
	private $expanded;
	private $multiple;
	private $choices;

	public function __construct($label, $expanded, $multiple, $choices) {
		$this->label = $label;
		$this->expanded = $expanded;
		$this->multiple = $multiple;
		$this->choices = $choices;
	}

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
			->add('value', 'choice', array('data_class' => 'Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField', 'label' => $this->label, 'expanded' => $this->expanded, 'multiple' => $this->multiple, 'choices' => $this->choices))
        ;
    }

    public function getName()
    {
        return 'kunstmaan_formbundle_choiceformsubmissiontype';
    }
}

?>