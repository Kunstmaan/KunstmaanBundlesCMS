<?php

namespace Kunstmaan\AdminBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * An Acl changeset will be added to the queue whenever a change is made to the permissions. The {@link ApplyAclCommand}
 * will execute these changesets and change their status when finished.
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\AclChangesetRepository")
 * @ORM\Table(name="kuma_acl_changesets", indexes={@ORM\Index(name="idx_acl_changeset_ref", columns={"ref_id", "ref_entity_name"})})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class AclChangeset extends AbstractEntity
{
    /**
     * This changeset still needs to be applied
     */
    const STATUS_NEW = 0;

    /**
     * This changeset is currently being applied
     */
    const STATUS_RUNNING = 1;

    /**
     * This changeset is applied
     */
    const STATUS_FINISHED = 2;

    /**
     * Something went wrong while applying the changeset
     */
    const STATUS_FAILED = 3;

    /**
     * @ORM\Column(type="bigint", name="ref_id")
     */
    protected $refId;

    /**
     * @ORM\Column(type="string", name="ref_entity_name")
     */
    protected $refEntityName;

    /**
     * The doctrine metadata is set dynamically in Kunstmaan\AdminBundle\EventListener\MappingListener
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
        $this->status = self::STATUS_NEW;
        $this->lastModified = $this->created = new DateTime('now');
    }

    /**
     * Set ACL changeset
     *
     * @param array $changeset the changeset to apply
     *
     * @return AclChangeset
     */
    public function setChangeset(array $changeset)
    {
        $this->changeset = $changeset;

        return $this;
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
     * @param DateTime $created
     *
     * @return AclChangeset
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get timestamp of creation
     *
     * @return DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set timestamp of last modification
     *
     * @param DateTime $lastModified
     *
     * @return AclChangeset
     */
    public function setLastModified($lastModified)
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    /**
     * Get timestamp of last modification
     *
     * @return DateTime
     */
    public function getLastModified()
    {
        return $this->lastModified;
    }

    /**
     * Set reference entity id
     *
     * @param int $refId
     *
     * @return AclChangeset
     */
    protected function setRefId($refId)
    {
        $this->refId = $refId;

        return $this;
    }

    /**
     * Get reference entity id
     *
     * @return int
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * Set reference entity name
     *
     * @param string $refEntityName
     *
     * @return AclChangeset
     */
    protected function setRefEntityName($refEntityName)
    {
        $this->refEntityName = $refEntityName;

        return $this;
    }

    /**
     * Get reference entity name
     *
     * @return string
     */
    public function getRefEntityName()
    {
        return $this->refEntityName;
    }

    /**
     * Set reference entity
     *
     * @param AbstractEntity $entity
     *
     * @return AclChangeset
     */
    public function setRef(AbstractEntity $entity)
    {
        $this->setRefId($entity->getId());
        $this->setRefEntityName(ClassLookup::getClass($entity));

        return $this;
    }

    /**
     * Set status, every change in status will trigger last modified to be updated
     *
     * @param int $status
     *
     * @return AclChangeset
     */
    public function setStatus($status)
    {
        $this->status = $status;
        $this->setLastModified(new DateTime('now'));

        return $this;
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
     *
     * @return AclChangeset
     */
    public function setPid($pid)
    {
        $this->pid = $pid;

        return $this;
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
     * @param BaseUser $user
     *
     * @return AclChangeset
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return BaseUser
     */
    public function getUser()
    {
        return $this->user;
    }
}
