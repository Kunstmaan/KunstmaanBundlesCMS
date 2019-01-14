<?php

namespace Kunstmaan\AdminListBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminListBundle\Repository\LockableEntityRepository")
 * @ORM\Table(name="kuma_lockable_entity",
 *    uniqueConstraints={@ORM\UniqueConstraint(name="ix_kuma_lockable_entity_id_class", columns={"entity_id", "entity_class"})},
 *    indexes={@ORM\Index(name="idx__lockable_entity_id_class", columns={"entity_id", "entity_class"})}
 * )
 */
class LockableEntity extends AbstractEntity
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @var string
     *
     * @ORM\Column(type="string", name="entity_class")
     */
    protected $entityClass;

    /**
     * @var int
     *
     * @ORM\Column(type="bigint", name="entity_id")
     */
    protected $entityId;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return LockableEntity
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return LockableEntity
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Get entityClass.
     *
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @param $entityClass
     *
     * @return LockableEntity
     */
    public function setEntityClass($entityClass)
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    /**
     * Get entityId.
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set integer
     *
     * @param $entityId
     *
     * @return LockableEntity
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }
}
