<?php

namespace {{ namespace }}\Entity\PageParts;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

/**
 * {{ pagepart }}
 *
 * @ORM\Entity
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}s")
 */
class {{ pagepart }} extends AbstractPagePart
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
        return '{{ bundle }}:PageParts:{{ pagepart }}/view.html.twig';
    }

    /**
     * Return the backend view
     *
     * @return string
     */
    public function getAdminView()
    {
        return '{{ bundle }}:PageParts:{{ pagepart }}/admin-view.html.twig';
    }

    /**
     * Returns the default form type for this FormSubmissionField
     *
     * @return string
     */
    public function getDefaultAdminType()
    {
        return {{ adminType }}::class;
    }
}
