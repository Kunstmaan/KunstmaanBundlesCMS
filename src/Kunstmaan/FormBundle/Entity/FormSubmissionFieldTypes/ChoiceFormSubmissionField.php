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
	 * @ORM\Column(name="cfsf_value", type="array", nullable=true)
	 */
	protected $value;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $expanded = false;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $multiple = false;

	/**
	 * @ORM\Column(type="array", nullable=true)
	 */
	protected $choices = array();


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
		$values = $this->getValue();
		$choices = $this->getChoices();

		if (!empty($values)) {
			if(is_array($values) && sizeof($values)>0) {
				$result = array();
				foreach ($values as $value) {
					$result[] = $choices[$value];
				}
				return implode(", ", $result);
			}
			else {
				return $choices[$values];
			}
		}
		return "";
	}

	public function isNull()
	{
		$values = $this->getValue();
		return empty($values) ? true : (is_array($values) ? sizeof($values) <= 0 : false);
	}

	/**
	 * @return array
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * @param array $value
	 */
	public function setValue($values = array())
	{
		$this->value = $values;
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
