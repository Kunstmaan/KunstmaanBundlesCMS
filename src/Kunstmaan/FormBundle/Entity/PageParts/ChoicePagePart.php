<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;
use Kunstmaan\FormBundle\Form\ChoicePagePartAdminType;
use Symfony\Component\Form\FormBuilder;
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
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return "KunstmaanFormBundle:ChoicePagePart:view.html.twig";
    }

    /**
     * {@inheritdoc}
     */
    public function adaptForm(FormBuilder $formBuilder, &$fields)
    {
        $sfsf = new StringFormSubmissionField();
        $sfsf->setFieldName("field_" . $this->getUniqueId());
        $sfsf->setLabel($this->getLabel());
        $data = $formBuilder->getData();
        $data['formwidget_' . $this->getUniqueId()] = $sfsf;
        $label = $this->getLabel();
        if ($this->getRequired()) {
            $label = $label . ' *';
        }
        $choices = explode("\n", $this->getChoices());
        $formBuilder->add('formwidget_' . $this->getUniqueId(), new ChoiceFormSubmissionType($label, $this->getExpanded(), $this->getMultiple(), $choices));
        $formBuilder->setData($data);
        if ($this->getRequired()) {
            $formBuilder->addValidator(
				new FormValidator($sfsf, $this,
					function (FormInterface $form, $sfsf, $thiss)
					{
						$value = $sfsf->getValue();
						if ($value != null && !is_string($value)) {
							$v = $form->get('formwidget_' . $thiss->getUniqueId())->get('value');
							$v->addError(new FormError($thiss->getErrormessageRequired()));
						}
					}
		));
	}
        $fields[] = $sfsf;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return new ChoicePagePartAdminType();
    }

}
