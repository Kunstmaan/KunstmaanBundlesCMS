<?php

namespace Kunstmaan\FormBundle\Entity;

use DateTime;

use Kunstmaan\NodeBundle\Entity\Node;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * The form submission
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_form_submissions")
 * @ORM\HasLifecycleCallbacks()
 */
class FormSubmission
{
    /**
     * This id of the form submission
     *
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * The ip address which created this form submission
     *
     * @ORM\Column(type="string", name="ip_address")
     */
    protected $ipAddress;

    /**
     * Link to the node of the form which created this form submission
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\NodeBundle\Entity\Node")
     * @ORM\JoinColumn(name="node_id", referencedColumnName="id")
     */
    protected $node;

    /**
     * The language of the form submission
     *
     * @ORM\Column(type="string")
     */
    protected $lang;

    /**
     * The date when the form submission was created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * The extra fields with their value, which where configured on the form which created this submission
     *
     * @ORM\OneToMany(targetEntity="FormSubmissionField", mappedBy="formSubmission")
     */
    protected $fields;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fields = new ArrayCollection();
        $this->setCreated(new DateTime());
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
     * Get the ip address which submitted this form submission
     *
     * @return string
     */
    public function getIpAddress()
    {
        return $this->ipAddress;
    }

    /**
     * Set the ip address
     *
     * @param string $ipAddress
     */
    public function setIpAddress($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * Get the node of the form which created this form submission
     *
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set the node of the form which created this form submission
     *
     * @param Node $node
     */
    public function setNode($node)
    {
        $this->node = $node;
    }

    /**
     * Sets the language of this form submission
     *
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = $lang;
    }

    /**
     * Get the language of this form submission
     *
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * Set the date when the form submission was created
     *
     * @param datetime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get the date when this form submission was created
     *
     * @return datetime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Returns the list of fields with their values
     *
     * @return FormSubmissionField[];
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * A string representation of this form submission
     *
     * @return string;
     */
    public function __toString()
    {
        return "FormSubmission";
    }

}
