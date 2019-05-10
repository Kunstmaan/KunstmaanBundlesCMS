<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;

/**
 * The ChoiceFormSubmissionField can be used to store one or more selected choices to a FormSubmission
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_choice_form_submission_fields")
 */
class ChoiceFormSubmissionField extends FormSubmissionField
{
    /**
     * @ORM\Column(name="cfsf_value", type="array", nullable=true)
     */
    protected $value;

    /**
     * If set to true, radio buttons or checkboxes will be rendered (depending on the multiple value). If false,
     * a select element will be rendered.
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $expanded = false;

    /**
     * If true, the user will be able to select multiple options (as opposed to choosing just one option).
     * Depending on the value of the expanded option, this will render either a select tag or checkboxes
     * if true and a select tag or radio buttons if false. The returned value will be an array.
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $multiple = false;

    /**
     * The choices that should be used by this field. The choices option is an array, where the array key
     * is the item value and the array value is the item's label.
     *
     * @ORM\Column(type="array", nullable=true)
     */
    protected $choices = array();

    /**
     * If true, this field will be required.
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $required = false;

    /**
     * Returns the default form type for this FormSubmissionField
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return ChoiceFormSubmissionType::class;
    }

    /**
     * Returns a string representation of this FormSubmissionField
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->isNull()) {
            $values = $this->getValue();
            $choices = $this->getChoices();

            if (is_array($values) && count($values) > 0) {
                $result = array();
                foreach ($values as $value) {
                    $result[] = array_key_exists($value, $choices) ? trim($choices[$value]) : $value;
                }

                return implode(', ', $result);
            } else {
                if (isset($choices[$values])) {
                    return trim($choices[$values]);
                }
            }
        }

        return '';
    }

    /**
     * Checks if the value of this field is null
     *
     * @return bool
     */
    public function isNull()
    {
        $values = $this->getValue();
        if (is_array($values)) {
            return empty($values) || count($values) <= 0;
        } elseif (is_string($values)) {
            return !isset($values) || trim($values) === '';
        } else {
            return is_null($values);
        }
    }

    /**
     * Get the value of this field
     *
     * @return array
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of this field
     *
     * @param array $values
     *
     * @return ChoiceFormSubmissionField
     */
    public function setValue($values = array())
    {
        $this->value = $values;

        return $this;
    }

    /**
     * Set the expanded value
     *
     * @param bool $expanded
     *
     * @return ChoiceFormSubmissionField
     */
    public function setExpanded($expanded)
    {
        $this->expanded = $expanded;

        return $this;
    }

    /**
     * Returns the current expanded value, by default this will be false
     *
     * @return bool
     */
    public function getExpanded()
    {
        return $this->expanded;
    }

    /**
     * Set the multiple value
     *
     * @param bool $multiple
     *
     * @return ChoiceFormSubmissionField
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Returns the current multiple value, by default this will be false
     *
     * @return bool
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Set the possible choices for this field
     *
     * @param array $choices
     *
     * @return ChoiceFormSubmissionField
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * Get the possible choices for this field
     *
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * Set the field as required or not
     *
     * @param bool $required
     *
     * @return ChoiceFormSubmissionField
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Get the field required status, by default this will be false
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }
}
