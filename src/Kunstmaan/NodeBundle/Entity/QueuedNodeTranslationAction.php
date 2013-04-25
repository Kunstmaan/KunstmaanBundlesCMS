<?php

namespace Kunstmaan\NodeBundle\Entity;

use DateTime;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * QueuedNodeTranslationAction
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_node_queued_node_translation_actions")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class QueuedNodeTranslationAction extends AbstractEntity
{

    const ACTION_PUBLISH   = 'publish';
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
     * @var integer
     *
     * @ORM\Column(name="user_id", type="integer")
     */
    protected $userId;

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
     * Set userId
     *
     * @param string $userId
     *
     * @return QueuedNodeTranslationAction
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set date
     *
     * @param DateTime $date
     *
     * @return QueuedNodeTranslationAction
     */
    public function setDate(DateTime $date)
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
