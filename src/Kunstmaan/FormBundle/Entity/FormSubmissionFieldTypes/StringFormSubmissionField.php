<?php

namespace Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes;
use Kunstmaan\FormBundle\Form\StringFormSubmissionType;

use Kunstmaan\FormBundle\Entity\FormSubmissionField;

use Doctrine\ORM\EntityManager;

use Kunstmaan\AdminBundle\Modules\ClassLookup;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Type;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="form_stringformsubmissionfield")
 */

class StringFormSubmissionField extends FormSubmissionField {

	/**
	 * @ORM\Column(type="string")
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
		return new StringFormSubmissionType();
	}

	public function __toString() {
		if(is_null($this->getValue())){
			return "";
		}
		return $this->getValue();
	}

}
