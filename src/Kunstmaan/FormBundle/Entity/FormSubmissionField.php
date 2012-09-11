<?php

namespace Kunstmaan\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Form;

/**
 * The formsubmission field
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\FormBundle\Repository\FormSubmissionFieldRepository")
 * @ORM\Table(name="form_formsubmissionfield")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
         "string" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField" ,
        "text" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\TextFormSubmissionField",
        "choice" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField",
        "file" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField"
 * })
 */
class FormSubmissionField
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $fieldName;

    /**
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * @ORM\ManyToOne(targetEntity="FormSubmission", inversedBy="fields")
     * @ORM\JoinColumn(name="formsubmission", referencedColumnName="id")
     */
    protected $formsubmission;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param string $fieldName
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getSubmission()
    {
        return $this->formsubmission;
    }

    /**
     * @param string $formsubmission
     */
    public function setSubmission(FormSubmission $formsubmission)
    {
        $this->formsubmission = $formsubmission;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "FormSubmission Field";
    }

    /**
     * @param Form               $form        the Form
     * @param FormBuilder        $formBuilder the FormBuilder
     * @param Request            $request     the Request
     * @param ContainerInterface $container   the Container
     */
    public function onValidPost(Form $form, FormBuilder $formBuilder, Request $request, ContainerInterface $container)
    {
        // do nothing by default
    }
}
