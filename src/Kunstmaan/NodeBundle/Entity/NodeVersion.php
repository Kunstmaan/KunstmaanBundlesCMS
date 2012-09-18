<?php

namespace Kunstmaan\AdminNodeBundle\Entity;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\ClassLookup;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminNodeBundle\Entity\NodeTranslation;

/**
 * NodeVersion
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminNodeBundle\Repository\NodeVersionRepository")
 * @ORM\Table(name="kuma_node_versions")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class NodeVersion extends AbstractEntity
{

    const DRAFT_VERSION     = 'draft';
    const PUBLIC_VERSION    = 'public';

    /**
     * @ORM\ManyToOne(targetEntity="NodeTranslation")
     * @ORM\JoinColumn(name="node_translation_id", referencedColumnName="id")
     */
    protected $nodeTranslation;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="bigint")
     */
    protected $version;

    /**
     * @ORM\Column(type="string")
     */
    protected $owner;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updated;

    /**
     * @ORM\Column(type="bigint", name="ref_id")
     */
    protected $refId;

    /**
     * @ORM\Column(type="string", name="ref_entity_name")
     */
    protected $refEntityName;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setCreated(new \DateTime());
        $this->setUpdated(new \DateTime());
    }

    /**
     * Set nodeTranslation
     *
     * @param NodeTranslation $nodeTranslation
     *
     * @return NodeVersion
     */
    public function setNodeTranslation(NodeTranslation $nodeTranslation)
    {
        $this->nodeTranslation = $nodeTranslation;

        return $this;
    }

    /**
     * Get NodeTranslation
     *
     * @return NodeTranslation
     */
    public function getNodeTranslation()
    {
        return $this->nodeTranslation;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return NodeVersion
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set version
     *
     * @param string $version
     *
     * @return NodeVersion
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Set owner
     *
     * @param string $owner
     *
     * @return NodeVersion
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
     * Set created
     *
     * @param \DateTime $created
     *
     * @return NodeVersion
     */
    public function setCreated($created)
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
     * @return NodeVersion
     */
    public function setUpdated($updated)
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
     * @ORM\PreUpdate
     */
    public function preUpdate()
    {
        $this->setUpdated(new \DateTime());
    }

    /**
     * Get refId
     *
     * @return int
     */
    public function getRefId()
    {
        return $this->refId;
    }

    /**
     * Set refId
     *
     * @param int $refId
     *
     * @return NodeVersion
     */
    protected function setRefId($refId)
    {
        $this->refId = $refId;

        return $this;
    }

    /**
     * Set reference entity name
     *
     * @param string $refEntityName
     *
     * @return NodeVersion
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
     * @return null
     */
    public function getDefaultAdminType()
    {
        return null;
    }

    /**
     * @param AbstractEntity $entity
     */
    public function setRef(AbstractEntity $entity)
    {
        $this->setRefId($entity->getId());
        $this->setRefEntityName(ClassLookup::getClass($entity));
    }

    /**
     * @param EntityManager $em
     *
     * @return object
     */
    public function getRef(EntityManager $em)
    {
        return $em->getRepository($this->getRefEntityName())->find($this->getRefId());
    }
}
