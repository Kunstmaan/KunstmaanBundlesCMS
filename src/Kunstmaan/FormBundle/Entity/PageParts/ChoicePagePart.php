<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Symfony\Component\Form\FormBuilderInterface;
use ArrayObject;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;
use Kunstmaan\FormBundle\Form\ChoicePagePartAdminType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\Mapping as ORM;

/**
 * A choice pagepart
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_choice_page_parts")
 */
class ChoicePagePart extends AbstractFormPagePart
{

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $expanded;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $multiple;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $choices;

    /**
     * @ORM\Column(type="string", name="empty_value", nullable=true)
     */
    protected $emptyValue;

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanFormBundle:ChoicePagePart:view.html.twig";
    }

    /**
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
            $formBuilder->addValidator(
                new FormValidator($cfsf, $this,
                    function (FormInterface $form, ChoiceFormSubmissionField $cfsf, ChoicePagePart $thiss) {
                        if ($cfsf->isNull()) {
                            $errormsg = $thiss->getErrorMessageRequired();
                            $v = $form->get('formwidget_' . $thiss->getUniqueId())->get('value');
                            $formError = new FormError(empty($errormsg) ? AbstractFormPagePart::ERROR_REQUIRED_FIELD : $errormsg);
                            $v->addError($formError);
                        }
                    }
                ));
        }
        $fields[] = $cfsf;
    }

    /**
     * @return ChoicePagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new ChoicePagePartAdminType();
    }

    /**
     * @param bool $expanded
     */
    public function setExpanded($expanded)
    {
        $this->expanded = $expanded;
    }

    /**
     * @return bool
     */
    public function getExpanded()
    {
        return $this->expanded;
    }

    /**
     * @param bool $multiple
     */
    public function setMultiple($multiple)
    {
        $this->multiple = $multiple;
    }

    /**
     * @return boolean
     */
    public function getMultiple()
    {
        return $this->multiple;
    }

    /**
     * @param array $choices
     */
    public function setChoices($choices)
    {
        $this->choices = $choices;
    }

    /**
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

}
