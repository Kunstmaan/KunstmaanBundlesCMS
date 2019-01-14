<?php

namespace Kunstmaan\LeadGenerationBundle\Entity\Popup;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\EntityInterface;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_popup")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @UniqueEntity(fields={"name"})
 */
abstract class AbstractPopup implements EntityInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string an unique name for each popup
     * @ORM\Column(type="string", name="name", unique=true)
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string the html element id of the popup
     * @ORM\Column(type="string", name="html_id")
     * @Assert\NotBlank()
     */
    protected $htmlId;

    /**
     * @var ArrayCollection a list of rules that should be applied for this popup
     * @ORM\OneToMany(targetEntity="\Kunstmaan\LeadGenerationBundle\Entity\Rule\AbstractRule", mappedBy="popup", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $rules;

    public function __construct()
    {
        $this->rules = new ArrayCollection();
    }

    /**
     * @param int $id
     *
     * @return AbstractPopup
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return AbstractPopup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->htmlId;
    }

    /**
     * @param string $htmlId
     *
     * @return AbstractPopup
     */
    public function setHtmlId($htmlId)
    {
        $this->htmlId = $htmlId;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param ArrayCollection $rules
     *
     * @return AbstractPopup
     */
    public function setRules(ArrayCollection $rules)
    {
        $this->rules = $rules;

        return $this;
    }

    /**
     * @param AbstractRule $rule
     *
     * @return AbstractPopup
     */
    public function addRule(AbstractRule $rule)
    {
        $this->rules->add($rule);

        return $this;
    }

    /**
     * @param AbstractRule $rule
     *
     * @return AbstractPopup
     */
    public function removeRule(AbstractRule $rule)
    {
        $this->rules->removeElement($rule);

        return $this;
    }

    /**
     * @return int
     */
    public function getRuleCount()
    {
        return count($this->rules);
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
     * Get a list of available rules for this popup.
     * When null is returned, all rules are available.
     *
     * @return array|null
     */
    public function getAvailableRules()
    {
        return null;
    }

    /**
     * Should return the controller that should be executed.
     *
     * @return string
     */
    abstract public function getControllerAction();

    /**
     * @return string
     */
    abstract public function getAdminType();
}
