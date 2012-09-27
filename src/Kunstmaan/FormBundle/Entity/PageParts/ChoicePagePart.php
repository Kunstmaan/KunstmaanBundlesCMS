<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use ArrayObject;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;
use Kunstmaan\FormBundle\Form\ChoicePagePartAdminType;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;

use Doctrine\ORM\Mapping as ORM;

/**
 * The choice page part can be used to create forms with single or multiple choices. This can be
 * represented by a select box, input box or check boxes.
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_choice_page_parts")
 */
class ChoicePagePart extends AbstractFormPagePart
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
     * Returns the view used in the frontend
     *
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanFormBundle:ChoicePagePart:view.html.twig";
    }

    /**
     * Modify the form with the fields of the current page part
     *
     * @param FormBuilderInterface $formBuilder The form builder
     * @param ArrayObject          $fields      The fields
     */
    public function adaptForm(FormBuilderInterface $formBuilder, ArrayObject $fields)
    {
        $choices = explode("\n", $this->getChoices());

        $cfsf = new ChoiceFormSubmissionField();
        $cfsf->setFieldName("field_" . $this->getUniqueId());
        $cfsf->setLabel($this->getLabel());
        $cfsf->setChoices($choices);
        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $cfsf;
        $label = $this->getLabel();
        if ($this->getRequired()) {
            $label = $label . ' *';
        }

        $formBuilder->add('formwidget_' . $this->getUniqueId(), new ChoiceFormSubmissionType($label, $this->getExpanded(), $this->getMultiple(), $choices, $this->getEmptyValue()));
        $formBuilder->setData($data);
        if ($this->getRequired()) {
            $formBuilder->addEventListener(FormEvents::POST_BIND, function(FormEvent $formEvent) use ($cfsf, $this) {
                $form = $formEvent->getForm();

                if ($cfsf->isNull()) {
                    $errormsg = $this->getErrorMessageRequired();
                    $v = $form->get('formwidget_' . $this->getUniqueId())->get('value');
                    $formError = new FormError(empty($errormsg) ? AbstractFormPagePart::ERROR_REQUIRED_FIELD : $errormsg);
                    $v->addError($formError);
                }
            });
        }
        $fields[] = $cfsf;
    }

    /**
     * Returns the default backend form type for this FormSubmissionField
     *
     * @return ChoicePagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new ChoicePagePartAdminType();
    }

    /**
     * Set the expanded value, default this is false
     *
     * @param bool $expanded
     */
    public function setExpanded($expanded)
    {
        $this->expanded = $expanded;
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
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
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
     * @param array $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }

    /**
     * Get the current choices
     *
     * @return array
     */
    public function getChoices()
    {
        return $this->choices;
    }

    /**
     * Set emptyValue
     *
     * @param string $emptyValue
     */
    public function setEmptyValue($emptyValue)
    {
        $this->emptyValue = $emptyValue;
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
     */
    public function setRequired($required)
    {
        $this->required = $required;
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
     */
    public function setErrorMessageRequired($errorMessageRequired)
    {
        $this->errorMessageRequired = $errorMessageRequired;
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

}
