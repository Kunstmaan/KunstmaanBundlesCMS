<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Symfony\Component\Form\AbstractType;

use Kunstmaan\PagePartBundle\Helper\IsPagePart;
use Kunstmaan\FormBundle\Form\SubmitButtonPagePartAdminType;
use Symfony\Component\Form\FormBuilder;
use Kunstmaan\FormBundle\Entity\FormAdaptorIFace;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

/**
 * A submit button
 * 
 * @ORM\Entity
 * @ORM\Table(name="form_submitbutton")
 */
class SubmitButtonPagePart implements IsPagePart
{

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
	 * Set the id
	 * @param int $id
	 */
	public function setId($id)
	{
		$this->id = $id;
	}

	/**
	 * Get the id
	 * 
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set the label
	 * @param int $label
	 */
	public function setLabel($label)
	{
		$this->label = $label;
	}

	/**
	 * Get the label
	 *
	 * @return string
	 */
	public function getLabel()
	{
		return $this->label;
	}

    /**
     * @return string
     */
	public function __toString()
	{
		return "SubmitButtonPagePart";
	}

	/**
	 * @return string
	 */
	public function getDefaultView()
	{
		return "KunstmaanFormBundle:SubmitButtonPagePart:view.html.twig";
	}

	/**
	 * @return string
	 */
	public function getElasticaView()
	{
		return  $this->getDefaultView();
	}

	/**
	 * @return AbstractType
	 */
	public function getDefaultAdminType()
	{
		return new SubmitButtonPagePartAdminType();
	}
}
