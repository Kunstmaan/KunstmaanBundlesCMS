<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Kunstmaan\PagePartBundle\Helper\IsPagePart;
use Kunstmaan\FormBundle\Form\SubmitButtonPagePartAdminType;
use Symfony\Component\Form\FormBuilder;
use Kunstmaan\FormBundle\Entity\FormAdaptorIFace;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

/**
 *
 * @ORM\Entity
 * @ORM\Table(name="form_submitbutton")
 */
class SubmitButtonPagePart implements IsPagePart{

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

	public function __construct() {
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function getId() {
		return $this->id;
	}

	public function setLabel($label) {
		$this->label = $label;
	}

	public function getLabel() {
		return $this->label;
	}

	public function __toString() {
		return "SubmitButtonPagePart";
	}

	public function getDefaultView() {
		return "KunstmaanFormBundle:SubmitButtonPagePart:view.html.twig";
	}

	public function getElasticaView(){
		return  $this->getDefaultView();
	}
	
	public function getDefaultAdminType(){
		return new SubmitButtonPagePartAdminType();
	}
}
