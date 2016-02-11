<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\EmailFormSubmissionType;

/**
 * The EmailFormSubmissionField can be used to store email values to a FormSubmission
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_email_form_submission_fields")
 */
class EmailFormSubmissionField extends FormSubmissionField
{

    /**
     * @ORM\Column(name="efsf_value", type="string")
     */
    protected $value;

    /**
     * Returns the default form type for this FormSubmissionField
     *
     * @return EmailFormSubmissionType
     */
    public function getDefaultAdminType()
    {
        return new EmailFormSubmissionType();
    }

    /**
     * Return a string representation of this FormSubmissionField
     *
     * @return string
     */
    public function __toString()
    {
        $value = $this->getValue();

        return !empty($value) ? $value : "";
    }

    /**
     * Returns the current value of this field
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the current value of this field
     *
     * @param string $value
     *
     * @return EmailFormSubmissionField
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
