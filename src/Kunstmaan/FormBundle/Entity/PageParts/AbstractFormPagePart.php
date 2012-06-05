<?php

namespace Kunstmaan\FormBundle\Entity\PageParts;
use Kunstmaan\PagePartBundle\Entity\AbstractPagePart;

use Kunstmaan\FormBundle\Entity\FormAdaptorInterface;

use Kunstmaan\FormBundle\Entity\FormSubmissionFieldTypes\StringFormSubmissionField;
use Kunstmaan\FormBundle\Form\ChoiceFormSubmissionType;
use Kunstmaan\FormBundle\Form\ChoicePagePartAdminType;
use Symfony\Component\Form\FormBuilder;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\PagePartBundle\Form\HeaderPagePartAdminType;

/**
 * An abstract Form Pagepart
 */
abstract class AbstractFormPagePart extends AbstractPagePart implements FormAdaptorInterface
{

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
		$msg = $this->errormessageRequired;
        return empty($msg) ? "Required field!" : $msg;
    }

    /**
     * {@inheritdoc}
     */
    public function getElasticaView()
    {
        return $this->getDefaultView();
    }

}
