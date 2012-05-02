<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Symfony\Component\Form\FormValidatorInterface;
use Symfony\Component\Form\FormInterface;

class FormValidator implements FormValidatorInterface {

	private $callback;
	private $object;
	private $uniqueid;

	public function __construct($object, $uniqueid, $callback)
	{
		$this->callback = $callback;
		$this->uniqueid = $uniqueid;
		$this->object = $object;
	}

	function validate(FormInterface $form){
		return call_user_func($this->callback, $form, $this->object, $this->uniqueid);
	}
}

