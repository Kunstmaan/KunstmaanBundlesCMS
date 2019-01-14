<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\TextFormSubmissionType;

/**
 * The TextFormSubmissionField can be used to store multi-line string values to a FormSubmission
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_text_form_submission_fields")
 */
class TextFormSubmissionField extends FormSubmissionField
{
    /**
     * @ORM\Column(name="tfsf_value", type="text")
     */
    protected $value;

    /**
     * Return the current string value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets the string value for this FormSubmissionField
     *
     * @param string $value
     *
     * @return TextFormSubmissionField
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Returns the default form type for this FormSubmissionField
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return TextFormSubmissionType::class;
    }

    /**
     * Returns a string representation of this FormSubmissionField
     *
     * @return string
     */
    public function __toString()
    {
        $value = $this->getValue();

        return !empty($value) ? $value : '';
    }
}
