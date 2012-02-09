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
	 * @ORM\Column(type="bigint")
	 */
	protected $pageId;

	/**
	 * @ORM\Column(type="string")
	 */
	protected $pageEntityname;

	/**
	 * @ORM\Column(type="datetime")
	 */
	protected $created;

	/**
	 * @ORM\OneToMany(targetEntity="FormSubmissionField", mappedBy="formsubmission")
	 */
	protected $fields;

	public function __construct() {
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
	public function getPageId() {
		return $this->pageId;
	}

	/**
	 *
	 * @param string $refId
	 */
	public function setPageId($refId) {
		$this->pageId = $refId;
	}

	/**
	 *
	 * @param string $refEntityname
	 */
	public function setPageEntityname($pageEntityname) {
		$this->pageEntityname = $pageEntityname;
	}

	/**
	 *
	 * @return string
	 */
	public function getPageEntityname() {
		return $this->pageEntityname;
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

	public function __toString() {
		return "FormSubmission";
	}

}
