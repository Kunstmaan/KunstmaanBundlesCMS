<?php

namespace Kunstmaan\NodeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Entity\BaseUser;

/**
 * QueuedNodeTranslationAction
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_node_queued_node_translation_actions")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class QueuedNodeTranslationAction extends AbstractEntity
{
    const ACTION_PUBLISH = 'publish';
    const ACTION_UNPUBLISH = 'unpublish';

    /**
     * @var NodeTranslation
     *
     * @ORM\ManyToOne(targetEntity="NodeTranslation")
     * @ORM\JoinColumn(name="node_translation_id", referencedColumnName="id")
     */
    protected $nodeTranslation;

    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    protected $action;

    /**
     * @var BaseUser
     *
     * The doctrine metadata is set dynamically in Kunstmaan\NodeBundle\EventListener\MappingListener
     */
    protected $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * Set nodeTranslation
     *
     * @param NodeTranslation $nodeTranslation
     *
     * @return QueuedNodeTranslationAction
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
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return QueuedNodeTranslationAction
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Set user
     *
     * @param BaseUser $user
     *
     * @return QueuedNodeTranslationAction
     */
    public function setUser(BaseUser $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return QueuedNodeTranslationAction
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return DateTime
     */
    public function getDate()
    {
        return $this->date;
    }
}
