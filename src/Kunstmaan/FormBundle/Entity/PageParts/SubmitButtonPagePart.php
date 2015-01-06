<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Kunstmaan\FormBundle\Form\SubmitButtonPagePartAdminType;
use Doctrine\ORM\Mapping as ORM;

/**
 * This page part adds a submit button to the forms
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_submit_button_page_parts")
 */
class SubmitButtonPagePart extends AbstractPagePart
{

    /**
     * The label on the submit button
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $label;

    /**
     * Set the label
     *
     * @param int $label
     *
     * @return SubmitButtonPagePart
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
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
     * Return a string representation of this page part
     *
     * @return string
     */
    public function __toString()
    {
        return "SubmitButtonPagePart";
    }

    /**
     * Return the frontend view
     *
     * @return string
     */
    public function getDefaultView()
    {
        return "KunstmaanFormBundle:SubmitButtonPagePart:view.html.twig";
    }

    /**
     * Return the backend view
     *
     * @return string
     */
    public function getAdminView()
    {
        return "KunstmaanFormBundle:SubmitButtonPagePart:view-admin.html.twig";
    }

    /**
     * Returns the default form type for this FormSubmissionField
     *
     * @return SubmitButtonPagePartAdminType
     */
    public function getDefaultAdminType()
    {
        return new SubmitButtonPagePartAdminType();
    }

}
