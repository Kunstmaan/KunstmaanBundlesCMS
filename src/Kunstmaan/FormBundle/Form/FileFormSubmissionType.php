<?php
namespace Kunstmaan\FormBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Symfony\Component\Form\AbstractType;

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
	public function buildForm(FormBuilderInterface $builder, array $options)
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
