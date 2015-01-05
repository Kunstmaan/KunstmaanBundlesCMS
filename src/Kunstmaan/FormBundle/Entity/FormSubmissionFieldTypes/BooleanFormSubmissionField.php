<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\BooleanFormSubmissionType;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;

/**
 * The BooleanFormSubmissionField can be used to store one or more selected choices to a FormSubmission
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_boolean_form_submission_fields")
 */
class BooleanFormSubmissionField extends FormSubmissionField
{

    /**
     * @ORM\Column(name="bfsf_value", type="boolean", nullable=true)
     */
    protected $value;

    /**
     * Returns the default form type for this FormSubmissionField
     *
     * @return ChoiceFormSubmissionType
     */
    public function getDefaultAdminType()
    {
        return new BooleanFormSubmissionType();
    }

    /**
     * Returns a string representation of this FormSubmissionField
     *
     * @return string
     */
    public function __toString()
    {
        $value = $this->getValue();
        if (empty($value)) {
            return "false";
        } else {
            return "true";
        }
    }

    /**
     * Get the value of this field
     *
     * @return boolean
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set the value of this field
     *
     * @param boolean $value
     *
     * @return BooleanFormSubmissionField
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
