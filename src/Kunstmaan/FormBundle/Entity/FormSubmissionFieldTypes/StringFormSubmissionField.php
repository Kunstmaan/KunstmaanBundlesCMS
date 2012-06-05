<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Type;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * StringFormSubmissionField
 * 
 * @ORM\Entity
 * @ORM\Table(name="form_stringformsubmissionfield")
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
     * @return \Kunstmaan\FormBundle\Form\StringFormSubmissionType
     */
    public function getDefaultAdminType()
    {
        return new StringFormSubmissionType($this->getLabel());
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
		$value = $this->getValue();
        return (defined($value) && !is_null($value)) ? $value : "";
    }

}
