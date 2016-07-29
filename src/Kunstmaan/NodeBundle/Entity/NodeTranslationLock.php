<?php

namespace Kunstmaan\NodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\User;

/**
 * NodeTranslationLock
 *
 * @ORM\Table(name="kuma_node_translation_lock")
 * @ORM\Entity(repositoryClass="Kunstmaan\NodeBundle\Repository\NodeTranslationLockRepository")
 */
class NodeTranslationLock extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\AdminBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var NodeTranslation
     *
     * @ORM\ManyToOne(targetEntity="Kunstmaan\NodeBundle\Entity\NodeTranslation")
     * @ORM\JoinColumn(name="node_translation_id", referencedColumnName="id")
     */
    private $nodeTranslation;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return NodeTranslationLock
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
     * Set user
     *
     * @param \Kunstmaan\AdminBundle\Entity\User $user
     *
     * @return NodeTranslationLock
     */
    public function setUser(\Kunstmaan\AdminBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Kunstmaan\AdminBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set nodeTranslation
     *
     * @param \Kunstmaan\NodeBundle\Entity\NodeTranslation $nodeTranslation
     *
     * @return NodeTranslationLock
     */
    public function setNodeTranslation(\Kunstmaan\NodeBundle\Entity\NodeTranslation $nodeTranslation = null)
    {
        $this->nodeTranslation = $nodeTranslation;

        return $this;
    }

    /**
     * Get nodeTranslation
     *
     * @return \Kunstmaan\NodeBundle\Entity\NodeTranslation
     */
    public function getNodeTranslation()
    {
        return $this->nodeTranslation;
    }
}
