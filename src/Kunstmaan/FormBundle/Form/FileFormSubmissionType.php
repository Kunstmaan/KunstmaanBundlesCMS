<?php
namespace Kunstmaan\FormBundle\Form;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FileFormSubmissionType extends AbstractType
{
	protected $label;
	protected $required;

	public function __construct($label, $required)
	{
		$this->label = $label;
		$this->required = $required;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilder $builder, array $options)
	{
		$builder->add('file', 'file', array(
			'label' => $this->label,
			'required' => $this->required
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function getName()
	{
		return 'kunstmaan_formbundle_fileformsubmissiontype';
	}
}
