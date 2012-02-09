<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;

use Kunstmaan\FormBundle\Form\SingleLineTextPagePartAdminType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;

use Symfony\Component\Form\CallbackValidator;

use Symfony\Component\Form\FormBuilder;

use Kunstmaan\AdminBundle\Modules\ClassLookup;

use Kunstmaan\FormBundle\Entity\FormAdaptorIFace;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="form_singlelinetextpagepart")
 */
class SingleLineTextPagePart implements FormAdaptorIFace {

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
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $regex;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	protected $errormessage_regex;

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

	public function setRegex($regex) {
		$this->regex = $regex;
	}

	public function getRegex() {
		return $this->regex;
	}

	public function setErrormessageRegex($errormessage_regex) {
		$this->errormessage_regex = $errormessage_regex;
	}

	public function getErrormessageRegex() {
		return $this->errormessage_regex;
	}

	public function __toString() {
		return "SingleLineTextPagePart ";
	}

	public function getDefaultView() {
		return "KunstmaanFormBundle:SingleLineTextPagePart:view.html.twig";
	}

	public function adaptForm(FormBuilder $formBuilder, &$fields) {
		$sfsf = new StringFormSubmissionField();
		$sfsf->setFieldName("field_".$this->getUniqueId());
		$data = $formBuilder->getData();
		$data['formwidget_'.$this->getUniqueId()] = $sfsf;
		$label = $this->getLabel();
		if($this->getRequired()){
			$label = $label.' *';
		}
		$formBuilder->add('formwidget_'.$this->getUniqueId(), new StringFormSubmissionType($label));
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
		if($this->getRegex()){
			$formBuilder->addValidator(new FormValidator($sfsf, $this, function(FormInterface $form, $sfsf, $thiss) {
				$value = $sfsf->getValue();
				if ($value != null && is_string($value) && !preg_match('/'.$thiss->getRegex().'/', $value)) {
					$v = $form->get('formwidget_'.$thiss->getUniqueId())->get('value');
					$v->addError(new FormError($thiss->getErrormessageRegex()));
				}
			}));
		}
		$fields[] = $sfsf;
	}

	public function getDefaultAdminType(){
		return new SingleLineTextPagePartAdminType();
	}

}
