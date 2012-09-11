<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;

use Kunstmaan\AdminBundle\Modules\ClassLookup;
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
     * @ORM\Column(type="string", name="errormessage_required", nullable=true)
     */
    protected $errormessageRequired;

    /**
     * {@inheritdoc}
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
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return boolean
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param string $errormessageRequired
     */
    public function setErrormessageRequired($errormessageRequired)
    {
        $this->errormessageRequired = $errormessageRequired;
    }

    /**
     * @return string
     */
    public function getErrormessageRequired()
    {
        return $this->errormessageRequired;
    }

    /**
     * {@inheritdoc}
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

    public function getAdminView()
    {
        return "KunstmaanFormBundle:AbstractFormPagePart:admin-view.html.twig";
    }

}
