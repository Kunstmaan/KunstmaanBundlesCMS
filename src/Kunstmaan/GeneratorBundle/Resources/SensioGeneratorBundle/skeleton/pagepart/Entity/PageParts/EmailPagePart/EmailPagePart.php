<?php

namespace {{ namespace }}\Entity\PageParts;

use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\EmailFormSubmissionField;
use Kunstmaan\FormBundle\Form\EmailFormSubmissionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Kunstmaan\FormBundle\Entity\PageParts\AbstractFormPagePart;

/**
 * {{ pagepart }}
 *
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 */
class {{ pagepart }} extends AbstractFormPagePart
{
    /**
     * If set to true, you are obligated to fill in this page part
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $required = false;

    /**
     * Error message shows when the page part is required and nothing is filled in
     *
     * @ORM\Column(type="string", name="error_message_required", nullable=true)
     */
    protected $errorMessageRequired;

    /**
     * Error message shows when the value is invalid
     *
     * @ORM\Column(type="string", name="error_message_invalid", nullable=true)
     */
    protected $errorMessageInvalid;

    /**
     * Internal name that can be used with form submission subscribers.
     *
     * @ORM\Column(type="string", name="internal_name", nullable=true)
     */
    protected $internalName;

    /**
     * Sets the required value of this page part
     *
     * @param bool $required
     *
     * @return EmailPagePart
     */
    public function setRequired($required)
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Check if the page part is required
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Sets the message shown when the page part is required and no value was entered
     *
     * @param string $errorMessageRequired
     *
     * @return EmailPagePart
     */
    public function setErrorMessageRequired($errorMessageRequired)
    {
        $this->errorMessageRequired = $errorMessageRequired;

        return $this;
    }

    /**
     * Get the error message that will be shown when the page part is required and no value was entered
     *
     * @return string
     */
    public function getErrorMessageRequired()
    {
        return $this->errorMessageRequired;
    }

    /**
     * Sets the message shown when the value is invalid
     *
     * @param string $errorMessageInvalid
     *
     * @return EmailPagePart
     */
    public function setErrorMessageInvalid($errorMessageInvalid)
    {
        $this->errorMessageInvalid = $errorMessageInvalid;

        return $this;
    }

    /**
     * Get the error message that will be shown when the value is invalid
     *
     * @return string
     */
    public function getErrorMessageInvalid()
    {
        return $this->errorMessageInvalid;
    }

    /**
     * @param string $internalName
     *
     * @return self
     */
    public function setInternalName($internalName)
    {
        $this->internalName = $internalName;

        return $this;
    }

    /**
     * @return string
     */
    public function getInternalName()
    {
        return $this->internalName;
    }

    /**
     * Returns the frontend view
     *
     * @return string
     */
    public function getDefaultView()
    {
        return '{{ bundle }}:PageParts:{{ pagepart }}/view.html.twig';
    }

    /**
     * Modify the form with the fields of the current page part
     *
     * @param FormBuilderInterface $formBuilder The form builder
     * @param ArrayObject          $fields      The fields
     * @param int                  $sequence    The sequence of the form field
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields, $sequence)
    {
        $efsf = new EmailFormSubmissionField();
        $efsf->setFieldName("field_" . $this->getUniqueId());
        $efsf->setLabel($this->getLabel());
        $efsf->setSequence($sequence);
        $efsf->setInternalName($this->getInternalName());

        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $efsf;

        $constraints = array();
        if ($this->getRequired()) {
            $options = array();
            if (!empty($this->errorMessageRequired)) {
                $options['message'] = $this->errorMessageRequired;
            }
            $constraints[] = new NotBlank($options);
        }
        $options = array();
        if (!empty($this->errorMessageInvalid)) {
            $options['message'] = $this->getErrorMessageInvalid();
        }
        $constraints[] = new Email($options);

        $formBuilder->add('formwidget_' . $this->getUniqueId(),
            EmailFormSubmissionType::class,
            array(
                'label'       => $this->getLabel(),
                'constraints' => $constraints,
                'required'    => $this->getRequired()
            )
        );
        $formBuilder->setData($data);

        $fields->append($efsf);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return {{ adminType }}::class;
    }
}
