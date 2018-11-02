<?php

namespace {{ namespace }}\Entity\PageParts;

use ArrayObject;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;
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
     * If set to true, radio buttons or checkboxes will be rendered (depending on the multiple value). If false,
     * a select element will be rendered.
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $expanded = false;

    /**
     * If true, the user will be able to select multiple options (as opposed to choosing just one option).
     * Depending on the value of the expanded option, this will render either a select tag or checkboxes
     * if true and a select tag or radio buttons if false. The returned value will be an array.
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $multiple = false;

    /**
     * The choices that should be used by this field. The choices can be entered separated by a new line.
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $choices;

    /**
     * This option determines whether or not a special "empty" option (e.g. "Choose an option")
     * will appear at the top of a select widget. This option only applies if both the expanded and
     * multiple options are set to false.
     *
     * @ORM\Column(type="string", name="empty_value", nullable=true)
     */
    protected $emptyValue;

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
     * Returns the view used in the frontend
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
        $choices = explode("\n", $this->getChoices());
        $choices = array_map('trim', $choices);

        $cfsf = new ChoiceFormSubmissionField();
        $cfsf->setFieldName("field_" . $this->getUniqueId());
        $cfsf->setLabel($this->getLabel());
        $cfsf->setChoices($choices);
        $cfsf->setRequired($this->required);
        $cfsf->setSequence($sequence);
        $cfsf->setInternalName($this->getInternalName());

        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $cfsf;
        $constraints = array();
        if ($this->getRequired()) {
            $options = array();
            if (!empty($this->errorMessageRequired)) {
                $options['message'] = $this->errorMessageRequired;
            }
            $constraints[] = new NotBlank($options);
        }

        $formBuilder->add(
            'formwidget_' . $this->getUniqueId(),
            ChoiceFormSubmissionType::class,
            array(
                'label'       => $this->getLabel(),
                'required'    => $this->getRequired(),
                'expanded'    => $this->getExpanded(),
                'multiple'    => $this->getMultiple(),
                'choices'     => $choices,
                'placeholder' => $this->getEmptyValue(),
                'constraints' => $constraints,
            )
        );
        $formBuilder->setData($data);

        $fields->append($cfsf);
    }

    /**
     * Returns the default backend form type for this FormSubmissionField
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return {{ adminType }}::class;
    }

    /**
     * Set the expanded value, default this is false
     *
     * @param bool $expanded
     *
     * @return ChoicePagePart
     */
    public function setExpanded($expanded)
    {
        $this->expanded = $expanded;

        return $this;
    }

    /**
     * Get the expanded value
     *
     * @return bool
     */
    public function getExpanded()
    {
        return $this->expanded;
    }

    /**
     * Set the multple value, default this is false
     *
     * @param bool $multiple
     *
     * @return ChoicePagePart
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;

        return $this;
    }

    /**
     * Get the current multiple value
     *
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * Set the choices for this pagepart
     *
     * @param string $choices Seperated by '\n'
     *
     * @return ChoicePagePart
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;

        return $this;
    }

    /**
     * Get the current choices
     *
     * @return string Seperated by '\n'
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * Set emptyValue
     *
     * @param string $emptyValue
     *
     * @return ChoicePagePart
     */
    public function setEmptyValue($emptyValue)
    {
        $this->emptyValue = $emptyValue;

        return $this;
    }

    /**
     * Get emptyValue
     *
     * @return string
     */
    public function getEmptyValue()
    {
        return $this->emptyValue;
    }

    /**
     * Sets the required valud of this page part
     *
     * @param bool $required
     *
     * @return ChoicePagePart
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
     * @return ChoicePagePart
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
}
