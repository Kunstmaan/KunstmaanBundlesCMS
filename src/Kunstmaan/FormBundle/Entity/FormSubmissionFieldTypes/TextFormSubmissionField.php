<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;

use Kunstmaan\FormBundle\Entity\FormSubmissionField;
use Kunstmaan\FormBundle\Form\TextFormSubmissionType;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="form_textformsubmissionfield")
 */

class TextFormSubmissionField extends FormSubmissionField {

	/**
	 * @ORM\Column(type="text")
	 */
	protected $value;

	public function __construct() {
		parent::__construct();
	}

	/**
	 *
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 *
	 * @param string $refEntityname
	 */
	public function setValue($value) {
		$this->value = $value;
	}

	public function getDefaultAdminType(){
		return new TextFormSubmissionType();
	}

	public function __toString() {
		if(is_null($this->getValue())){
			return "";
		}
		return $this->getValue();
	}

}
