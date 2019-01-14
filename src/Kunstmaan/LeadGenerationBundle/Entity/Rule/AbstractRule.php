<?php

namespace Kunstmaan\LeadGenerationBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup;
use Kunstmaan\LeadGenerationBundle\Form\AbstractRuleAdminType;
use Kunstmaan\LeadGenerationBundle\Service\RuleServiceInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_popup_rules")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 */
abstract class AbstractRule
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="\Kunstmaan\LeadGenerationBundle\Entity\Popup\AbstractPopup", inversedBy="rules")
     * @ORM\JoinColumn(name="popup_id", referencedColumnName="id")
     **/
    protected $popup;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AbstractPopup
     */
    public function getPopup()
    {
        return $this->popup;
    }

    /**
     * @param AbstractPopup $popup
     *
     * @return AbstractRule
     */
    public function setPopup(AbstractPopup $popup)
    {
        $this->popup = $popup;

        return $this;
    }

    /**
     * @return string
     */
    public function getFullClassname()
    {
        return get_class($this);
    }

    /**
     * @return string
     */
    public function getClassname()
    {
        return basename(str_replace('\\', '/', get_class($this)));
    }

    /**
     * When the javascript code needs some properties that are not available in this entity, you can create a custom
     * service that implements the RuleServiceInterface to append extra properties.
     *
     * @return RuleServiceInterface|null
     */
    public function getService()
    {
        return null;
    }

    /**
     * Get the javascript object class name.
     *
     * @return string
     */
    abstract public function getJsObjectClass();

    /**
     * Get the properties that can be used in the javascript object.
     *
     * @return string
     */
    abstract public function getJsProperties();

    /**
     * Get the file path of the javascript file that should be included.
     *
     * @return string
     */
    abstract public function getJsFilePath();

    /**
     * Get the rule admin type
     *
     * @return AbstractRuleAdminType
     */
    abstract public function getAdminType();
}
