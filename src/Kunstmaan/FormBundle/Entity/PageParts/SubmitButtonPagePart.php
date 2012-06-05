<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Kunstmaan\PagePartBundle\Helper\PagePartInterface;
use Symfony\Component\Form\AbstractType;
use Kunstmaan\FormBundle\Form\SubmitButtonPagePartAdminType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

/**
 * A submit button
 *
 * @ORM\Entity
 * @ORM\Table(name="form_submitbutton")
 */
class SubmitButtonPagePart extends AbstractPagePart
{

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $label;

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

	public function getAdminView()
	{
		return "KunstmaanFormBundle:SubmitButtonPagePart:view-admin.html.twig";
	}

	/**
     * @return string
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * @return AbstractType
     */
    public function getDefaultAdminType()
    {
        return new SubmitButtonPagePartAdminType();
    }

}
