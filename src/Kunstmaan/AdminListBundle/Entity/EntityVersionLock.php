<?php

namespace Kunstmaan\AdminListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntityVersionLock
 *
 * @ORM\Table(name="kuma_entity_version_lock", indexes={
 *     @ORM\Index(name="evl_owner_entity_idx", columns={"owner", "lockable_entity_id"}),
 * })
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminListBundle\Repository\EntityVersionLockRepository")
 */
class EntityVersionLock extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="owner", type="string", length=255)
     */
    private $owner;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var LockableEntity
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\AdminListBundle\Entity\LockableEntity")
     * @ORM\JoinColumn(name="lockable_entity_id", referencedColumnName="id")
     */
    private $lockableEntity;

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return EntityVersionLock
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set owner
     *
     * @param string
     *
     * @return EntityVersionLock
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return string
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @return LockableEntity
     */
    public function getLockableEntity()
    {
        return $this->lockableEntity;
    }

    /**
     * @param LockableEntity $lockableEntity
     *
     * @return EntityVersionLock
     */
    public function setLockableEntity($lockableEntity)
    {
        $this->lockableEntity = $lockableEntity;

        return $this;
    }
}
