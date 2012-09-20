<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Kunstmaan\AdminBundle\Helper\ClassLookup;
use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract version of a form page part
 */
abstract class AbstractFormPagePart extends AbstractPagePart implements FormAdaptorInterface
{

    const ERROR_REQUIRED_FIELD = "field.required";

    /**
     * The label
     *
     * @ORM\Column(type="string", nullable=true)
     */
    protected $label;

    /**
     * If set to true, you are obligated to fill in this page part
     *
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $required;

    /**
     * Error message shows when the page part is required and nothing is filled in
     *
     * @ORM\Column(type="string", name="error_message_required", nullable=true)
     */
    protected $errorMessageRequired;

    /**
     * Returns a unique id for the current page part
     *
     * @return string
     */
    public function getUniqueId()
    {
        return ClassLookup::getClass($this) . $this->id; //TODO
    }

    /**
     * Set the label used for this page part
     *
     * @param int $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get the label used for this page part
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Sets the required valud of this page part
     *
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * Check if the page part is required
     *
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * Sets the message shown when the page part is required and no value was entered
     *
     * @param string $errorMessageRequired
     */
    public function setErrorMessageRequired($errorMessageRequired)
    {
        $this->errorMessageRequired = $errorMessageRequired;
    }

    /**
     * Get the error message that will be shown when the page part is required and no value was entered
     *
     * @return string
     */
    public function getErrorMessageRequired()
    {
        return $this->errorMessageRequired;
    }

    /**
     * Returns the view for searches
     *
     * @return string
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * Returns the view used in the backend
     *
     * @return string
     */
    public function getAdminView()
    {
        return "KunstmaanFormBundle:AbstractFormPagePart:admin-view.html.twig";
    }

}
