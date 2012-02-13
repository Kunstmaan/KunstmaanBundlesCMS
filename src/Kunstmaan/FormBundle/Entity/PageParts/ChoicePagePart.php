<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;
use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;

use Kunstmaan\FormBundle\Form\ChoicePagePartAdminType;

use Symfony\Component\Form\FormBuilder;

use Kunstmaan\AdminBundle\Modules\ClassLookup;

use Kunstmaan\FormBundle\Entity\FormAdaptorIFace;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="form_choicepagepart")
 */
class ChoicePagePart implements FormAdaptorIFace {

	/**
	 * @ORM\Id
	 * @ORM\Column(type="bigint")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $label;

	/**
	 * @ORM\Column(type="boolean", nullable=true)
	 */
	protected $required;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $errormessage_required;

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

	public function __construct() {
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function getUniqueId(){
		return ClassLookup::getClass($this).$this->id; //TODO
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getLabel() {
		return $this->label;
	}

	public function setExpanded($expanded){
		$this->expanded = $expanded;
	}

	public function getExpanded(){
		return $this->expanded;
	}

	public function setRequired($required){
		$this->required = $required;
	}

	public function getRequired(){
		return $this->required;
	}

	public function setErrormessageRequired($errormessage_required) {
		$this->errormessage_required = $errormessage_required;
	}

	public function getErrormessageRequired() {
		return $this->errormessage_required;
	}

	public function setMultiple($multiple){
		$this->multiple = $multiple;
	}

	public function getMultiple(){
		return $this->multiple;
	}

	public function setChoices($choices){
		$this->choices = $choices;
	}

	public function getChoices(){
		return $this->choices;
	}

	public function __toString() {
		return "MultiLineTextPagePart ";
	}

	public function getDefaultView() {
		return "KunstmaanFormBundle:ChoicePagePart:view.html.twig";
	}

	public function adaptForm(FormBuilder $formBuilder, &$fields) {
		$sfsf = new StringFormSubmissionField();
		$sfsf->setFieldName("field_".$this->getUniqueId());
		$sfsf->setLabel($this->getLabel());
		$data = $formBuilder->getData();
		$data['formwidget_'.$this->getUniqueId()] = $sfsf;
		$label = $this->getLabel();
		if($this->getRequired()){
			$label = $label.' *';
		}
		$choices = explode("\n", $this->getChoices());
		$formBuilder->add('formwidget_'.$this->getUniqueId(), new ChoiceFormSubmissionType($label, $this->getExpanded(), $this->getMultiple(), $choices));
		$formBuilder->setData($data);
		if($this->getRequired()){
			$formBuilder->addValidator(new FormValidator($sfsf, $this, function(FormInterface $form, $sfsf, $thiss) {
				$value = $sfsf->getValue();
				if ($value != null && !is_string($value)) {
					$v = $form->get('formwidget_'.$thiss->getUniqueId())->get('value');
					$v->addError(new FormError($thiss->getErrormessageRequired()));
				}
			}));
		}
		$fields[] = $sfsf;
	}

	public function getDefaultAdminType(){
		return new ChoicePagePartAdminType();
	}

}
