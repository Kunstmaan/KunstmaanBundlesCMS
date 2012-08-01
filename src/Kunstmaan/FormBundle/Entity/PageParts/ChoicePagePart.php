<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Symfony\Component\Form\FormBuilderInterface;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\ChoiceFormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;
use Kunstmaan\FormBundle\Form\ChoicePagePartAdminType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

/**
 * A choice pagepart
 *
 * @ORM\Entity
 * @ORM\Table(name="form_choicepagepart")
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
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $empty_value;

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultView()
	{
		return "KunstmaanFormBundle:ChoicePagePart:view.html.twig";
	}

	/**
	 * {@inheritdoc}
	 */
	public function adaptForm(FormBuilderInterface $formBuilder, &$fields)
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
					function (FormInterface $form, $cfsf, $thiss)
					{
						if ($cfsf->isNull()) {
							$errormsg = $thiss->getErrormessageRequired();
							$v = $form->get('formwidget_' . $thiss->getUniqueId())->get('value');
							$v->addError(new FormError( empty($errormsg) ? AbstractFormPagePart::ERROR_REQUIRED_FIELD : $errormsg));
						}
					}
				));
		}
		$fields[] = $cfsf;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDefaultAdminType()
	{
		return new ChoicePagePartAdminType();
	}

    /**
     * @param boolean $expanded
     */
    public function setExpanded($expanded)
    {
        $this->expanded = $expanded;
    }

    /**
     * @return boolean
     */
    public function getExpanded()
    {
        return $this->expanded;
    }

    /**
     * @param boolean $multiple
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
     * Set empty_value
     *
     * @param string $emptyValue
     */
    public function setEmptyValue($emptyValue)
    {
        $this->empty_value = $emptyValue;
    }

    /**
     * Get empty_value
     *
     * @return string
     */
    public function getEmptyValue()
    {
        return $this->empty_value;
    }

}