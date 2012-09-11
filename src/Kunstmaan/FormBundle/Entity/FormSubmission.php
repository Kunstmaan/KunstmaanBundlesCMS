<?php

namespace Kunstmaan\FormBundle\Entity;

use Kunstmaan\AdminNodeBundle\Entity\Node;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * The form submission
 *
 * @ORM\Entity
 * @ORM\Table(name="form_formsubmission")
 * @ORM\HasLifecycleCallbacks()
 */
class FormSubmission
{
    /**
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $ipAddress;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\AdminNodeBundle\Entity\Node")
     * @ORM\JoinColumn(name="node", referencedColumnName="id")
     */
    protected $node;

    /**
     * @ORM\Column(type="string")
     */
    protected $lang;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\OneToMany(targetEntity="FormSubmissionField", mappedBy="formsubmission")
     */
    protected $fields;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new \Doctrine\Common\Collections\ArrayCollection();
        $this->setCreated(new \DateTime());
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * @param Node $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return array;
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return string;
     */
    public function __toString()
    {
        return "FormSubmission";
    }

}
