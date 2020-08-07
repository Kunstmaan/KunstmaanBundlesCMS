<?php

namespace Kunstmaan\VotingBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\VotingBundle\Entity\AbstractVote;
use Kunstmaan\VotingBundle\Event\EventInterface;

abstract class AbstractVoteListener
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var array
     */
    protected $actions;

    /**
     * @param EntityManagerInterface $em
     * @param array                  $actions
     */
    public function __construct(EntityManagerInterface $em, array $actions = array())
    {
        $this->em = $em;
        $this->actions = $actions;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param AbstractVote   $vote
     * @param EventInterface $event
     * @param int|null       $defaultValue
     */
    protected function createVote(AbstractVote $vote, EventInterface $event, $defaultValue = null)
    {
        $vote->setReference($event->getReference());
        if ($event->getRequest() !== null) {
            $vote->setIp($event->getRequest()->getClientIp());
        }
        if ($event->getValue() !== null) {
            $vote->setValue($event->getValue());
        } elseif ($defaultValue !== null) {
            $vote->setValue($defaultValue);
        }

        $this->em->persist($vote);
        $this->em->flush();
    }
}
