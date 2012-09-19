<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;

use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;
use Kunstmaan\AdminBundle\Helper\ClassLookup;
use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;

use Doctrine\ORM\Mapping as ORM;

/**
 * An abstract Form Pagepart
 */
abstract class AbstractFormPagePart extends AbstractPagePart implements FormAdaptorInterface
{

    const ERROR_REQUIRED_FIELD = "field.required";

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $label;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $required;

    /**
     * @ORM\Column(type="string", name="error_message_required", nullable=true)
     */
    protected $errorMessageRequired;

    /**
     * @return string
     */
    public function getUniqueId()
    {
        return ClassLookup::getClass($this) . $this->id; //TODO
    }

    /**
     * @param int $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param bool $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return bool
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param string $errorMessageRequired
     */
    public function setErrorMessageRequired($errorMessageRequired)
    {
        $this->errorMessageRequired = $errorMessageRequired;
    }

    /**
     * @return string
     */
    public function getErrorMessageRequired()
    {
        return $this->errorMessageRequired;
    }

    /**
     * @return string
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    /**
     * @return string
     */
    public function getAdminView()
    {
        return "KunstmaanFormBundle:AbstractFormPagePart:admin-view.html.twig";
    }

}
