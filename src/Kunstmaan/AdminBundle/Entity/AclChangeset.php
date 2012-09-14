<?php

namespace Kunstmaan\AdminBundle\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\AclChangesetRepository")
 * @ORM\Table(name="kuma_acl_changesets")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class AclChangeset extends AbstractEntity
{

    const STATUS_NEW      = 0;
    const STATUS_RUNNING  = 1;
    const STATUS_FINISHED = 2;
    const STATUS_FAILED   = 3;

    /**
     * @ORM\Column(type="bigint", name="ref_id")
     */
    protected $refId;

    /**
     * @ORM\Column(type="string", name="ref_entity_name")
     */
    protected $refEntityName;

    /**
     * @ORM\ManyToOne(targetEntity="Kunstmaan\AdminBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    protected $user;

    /**
     * @ORM\Column(type="array")
     */
    protected $changeset;

    /**
     * @ORM\Column(type="integer", name="pid", nullable=true)
     */
    protected $pid;

    /**
     * @ORM\Column(type="integer", name="status")
     */
    protected $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @ORM\Column(name="last_modified", type="datetime", nullable=true)
     */
    protected $lastModified;

    /**
     * Constructor, sets default status to STATUS_NEW & timestamps to current datetime
     */
    public function __construct()
    {
        $this->status       = self::STATUS_NEW;
        $this->lastModified = $this->created = new \DateTime('now');
    }

    /**
     * Set ACL changeset
     *
     * @param array $changeset the changeset to apply
     */
    public function setChangeset(array $changeset)
    {
        $this->changeset = $changeset;
    }

    /**
     * Get ACL changeset
     *
     * @return array
     */
    public function getChangeset()
    {
        return $this->changeset;
    }

    /**
     * Set timestamp of creation
     *
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * Get timestamp of creation
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set timestamp of last modification
     *
     * @param \DateTime $lastModified
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;
    }

    /**
     * Get timestamp of last modification
     *
     * @return \DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * @param int $refId
     */
    public function setRefId($refId)
    {
        $this->refId = $refId;
    }

    /**
     * @return int
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * @param string $refEntityName
     */
    public function setRefEntityName($refEntityName)
    {
        $this->refEntityName = $refEntityName;
    }

    /**
     * @return string
     */
    public function getRefEntityName()
    {
        return $this->refEntityName;
    }

    /**
     * Set status, every change in status will trigger last modified to be updated
     *
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
        $this->setLastModified(new \DateTime('now'));
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set process id
     *
     * @param int $pid
     */
    public function setPid($pid)
    {
        $this->pid = $pid;
    }

    /**
     * Get process id
     *
     * @return int
     */
    public function getPid()
    {
        return $this->pid;
    }

    /**
     * Set user
     *
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

}
