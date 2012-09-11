<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\TextFormSubmissionType;
use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a text form submission field
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
     * @return TextFormSubmissionType
     */
    public function getDefaultAdminType()
    {
        return new TextFormSubmissionType($this->getLabel());
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $value = $this->getValue();

        return !empty($value) ? $value : "";
    }

}
