<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;
use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\TextFormSubmissionType;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * TextFormSubmissionField
 * 
 * @ORM\Entity
 * @ORM\Table(name="form_textformsubmissionfield")
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
     * @return \Kunstmaan\FormBundle\Form\TextFormSubmissionType
     */
    public function getDefaultAdminType()
    {
        return new TextFormSubmissionType($this->getLabel());
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
