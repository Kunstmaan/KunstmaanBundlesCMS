<?php

namespace {{ namespace }}\Entity\PageParts;

use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\BooleanFormSubmissionField;
use Kunstmaan\FormBundle\Form\BooleanFormSubmissionType;
use Symfony\Component\Form\FormBuilderInterface;
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
     * Internal name that can be used with form submission subscribers.
     *
     * @ORM\Column(type="string", name="internal_name", nullable=true)
     */
    protected $internalName;

    /**
     * Sets the required valud of this page part
     *
     * @param bool $required
     *
     * @return CheckboxPagePart
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
     * @return CheckboxPagePart
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
        $bfsf = new BooleanFormSubmissionField();
        $bfsf->setFieldName('field_' . $this->getUniqueId());
        $bfsf->setLabel($this->getLabel());
        $bfsf->setInternalName($this->getInternalName());
        $bfsf->setSequence($sequence);

        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $bfsf;
        $constraints = array();
        if ($this->getRequired()) {
            $options = array();
            if (!empty($this->errorMessageRequired)) {
                $options['message'] = $this->errorMessageRequired;
            }
            $constraints[] = new NotBlank($options);
        }
        $formBuilder->add('formwidget_' . $this->getUniqueId(),
            BooleanFormSubmissionType::class,
            array(
                'label'       => $this->getLabel(),
                'constraints' => $constraints,
                'required'    => $this->getRequired()
            )
        );
        $formBuilder->setData($data);

        $fields->append($bfsf);
    }

    /**
     * Returns the default backend form type for this page part
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return {{ adminType }}::class;
    }
}
