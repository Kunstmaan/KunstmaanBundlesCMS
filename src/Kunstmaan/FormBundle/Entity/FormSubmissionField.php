<?php

namespace Kunstmaan\FormBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

/**
 * The FormSubmissionField will hold the submitted values from the form page parts. The FormSubmissionFields
 * will be attached to a FormSubmission.
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\FormBundle\Repository\FormSubmissionFieldRepository")
 * @ORM\Table(name="kuma_form_submission_fields")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "string" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField" ,
 *     "text" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\TextFormSubmissionField",
 *     "boolean" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField",
 *     "choice" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField",
 *     "file" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\FileFormSubmissionField",
 *     "email" = "Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField"
 * })
 */
abstract class FormSubmissionField
{
    /**
     * This id of this FormSubmissionField
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The name of this FormSubmissionField
     *
     * @ORM\Column(type="string")
     */
    protected $fieldName;

    /**
     * The label used for this FormSubmissionField
     *
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * The FormSubmission this field is part of
     *
     * @ORM\ManyToOne(targetEntity="FormSubmission", inversedBy="fields")
     * @ORM\JoinColumn(name="form_submission_id", referencedColumnName="id")
     */
    protected $formSubmission;

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
     *
     * @return FormSubmissionField
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the field name
     *
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * Set the field name
     *
     * @param string $fieldName
     *
     * @return FormSubmissionField
     */
    public function setFieldName($fieldName)
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    /**
     * Get the label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set the label
     *
     * @param string $label
     *
     * @return FormSubmissionField
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the FormSubmission this field is part of
     *
     * @return FormSubmission
     */
    public function getSubmission()
    {
        return $this->formSubmission;
    }

    /**
     * Set the FormSubmission this field is part of
     *
     * @param FormSubmission $formSubmission
     *
     * @return FormSubmissionField
     */
    public function setSubmission(FormSubmission $formSubmission)
    {
        $this->formSubmission = $formSubmission;

        return $this;
    }

    /**
     * Text representation of this field
     *
     * @return string
     */
    public function __toString()
    {
        return "FormSubmission Field";
    }

    /**
     * This function will be triggered if the form was successfully posted.
     *
     * @param Form                 $form        the Form
     * @param FormBuilderInterface $formBuilder the FormBuilder
     * @param Request              $request     the Request
     * @param ContainerInterface   $container   the Container
     */
    public function onValidPost(Form $form, FormBuilderInterface $formBuilder, Request $request, ContainerInterface $container)
    {
        // do nothing by default
    }
}
