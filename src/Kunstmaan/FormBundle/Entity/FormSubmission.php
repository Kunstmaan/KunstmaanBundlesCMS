<?php

namespace Kunstmaan\FormBundle\Entity;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Modules\ClassLookup;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\FormBundle\Repository\FormSubmissionRepository")
 * @ORM\Table(name="form_formsubmission")
 * @ORM\HasLifecycleCallbacks()
 */

class FormSubmission {
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

	public function __construct() {
		$this->fields = new \Doctrine\Common\Collections\ArrayCollection();
		$this->setCreated(new \DateTime());
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set id
	 *
	 * @param string $id
	 */
	public function setId($num) {
		$this->id = $num;
	}

	/**
	 *
	 * @return string
	 */
	public function getIpAddress() {
		return $this->ipAddress;
	}

	/**
	 *
	 * @param string $ipAddress
	 */
	public function setIpAddress($ipAddress) {
		$this->ipAddress = $ipAddress;
	}

	/**
	 *
	 * @return integer
	 */
	public function getNode() {
		return $this->node;
	}

	/**
	 *
	 * @param string $refId
	 */
	public function setNode($node) {
		$this->node = $node;
	}

	/**
	 *
	 * @param string $refEntityname
	 */
	public function setLang($lang) {
		$this->lang = $lang;
	}

	/**
	 *
	 * @return string
	 */
	public function getLang() {
		return $this->lang;
	}

	/**
	 *
	 * @param datetime $created
	 */
	public function setCreated($created) {
		$this->created = $created;
	}

	/**
	 *
	 * @return datetime
	 */
	public function getCreated() {
		return $this->created;
	}


	public function getFields() {
		return $this->fields;
	}

	public function __toString() {
		return "FormSubmission";
	}

}
