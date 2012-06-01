<?php
namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Doctrine\ORM\Mapping as ORM;

use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;

/**
 * @ORM\Entity
 * @ORM\Table(name="form_choiceformsubmissionfield")
 */
class ChoiceFormSubmissionField extends FormSubmissionField
{

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $value;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $expanded;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $multiple;

	/**
	 * @ORM\Column(type="text", nullable=true)
	 */
	protected $choices;


	/**
	 */
	public function getDefaultAdminType()
	{
		return new ChoiceFormSubmissionType($this->getLabel(), $this->getExpanded(), $this->getMultiple(), $this->getChoices());
	}

	/**
	 * {@inheritdoc}
	 */
	public function __toString()
	{
		return (is_null($this->getValue())) ? "" : $this->getValue();
	}
	/**
	 * @return string
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param string $value
	 */
	public function setValue($value)
	{
		$this->value = $value;
	}


	/**
	 * @param boolean $expanded
	 */
	public function setExpanded($expanded)
	{
		$this->expanded = $expanded;
	}

	/**
	 * @return boolean
	 */
	public function getExpanded()
	{
		return $this->expanded;
	}

	/**
	 * @param boolean $multiple
	 */
	public function setMultiple($multiple)
	{
		$this->multiple = $multiple;
	}

	/**
	 * @return boolean
	 */
	public function getMultiple()
	{
		return $this->multiple;
	}

	/**
	 * @param array $choices
	 */
	public function setChoices($choices)
	{
		$this->choices = $choices;
	}

	/**
	 * @return array
	 */
	public function getChoices()
	{
		return $this->choices;
	}
}
