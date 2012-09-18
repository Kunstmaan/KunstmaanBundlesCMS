<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Symfony\Component\Form\AbstractType;
use Kunstmaan\FormBundle\Form\SubmitButtonPagePartAdminType;
use Doctrine\ORM\Mapping as ORM;

/**
 * A submit button
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_submit_button_page_parts")
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

    /**
     * @return string
     */
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
     * @return SubmitButtonPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new SubmitButtonPagePartAdminType();
    }

}
