<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Symfony\Component\Validator\Constraints\Type;
use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a string form submission field
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_string_form_submission_fields")
 */
class StringFormSubmissionField extends FormSubmissionField
{

    /**
     * @ORM\Column(name="sfsf_value", type="string")
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
     * @return StringFormSubmissionType
     */
    public function getDefaultAdminType()
    {
        return new StringFormSubmissionType($this->getLabel());
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