<?php

namespace Kunstmaan\VotingBundle\EventListener\LinkedIn;

use Doctrine\ORM\EntityManager;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare;
use Symfony\Component\DependencyInjection\Container;

class LinkedInShareEventListener
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $em;

    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function onLinkedInShare(LinkedInShareEvent $event)
    {
        $vote = new LinkedInShare();
        $vote->setReference($event->getReference());
        if (!is_null($event->getRequest())) {
            $vote->setIp($event->getRequest()->getClientIp());
        }
        if ($event->getValue() !== null) {
            $vote->setValue($event->getValue());
        } else {
            $actions = $this->container->getParameter('kuma_voting.actions');
            if (isset($actions['linkedin_share'])) {
                $vote->setValue($actions['linkedin_share']['default_value']);
            }
        }

        $this->em->persist($vote);
        $this->em->flush();
    }

}
