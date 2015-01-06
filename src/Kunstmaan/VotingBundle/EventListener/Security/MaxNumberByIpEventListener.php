<?php

namespace Kunstmaan\VotingBundle\EventListener\Security;
use Kunstmaan\VotingBundle\Entity\AbstractVote;
use Symfony\Component\EventDispatcher\Event;
use Kunstmaan\VotingBundle\Event\EventInterface;

/**
* Security listener for prevent ip to vote more than maxnumber for an event
*/
class MaxNumberByIpEventListener
{

    /**
    * RepositoryResolver
    */
    protected $repositoryResolver;

    /**
    * Number
    */
    protected $maxNumber;

    /**
    * Constructor
    * @param RepositoryResolver $repositoryResolver entity manager
    * @param integer            $maxNumber          max number
    */
    public function __construct($repositoryResolver, $maxNumber)
    {
        $this->repositoryResolver = $repositoryResolver;
        $this->maxNumber = $maxNumber;
    }

    /**
    * On vote
    * @param EventInterface $event event
    */
    public function onVote(EventInterface $event)
    {

        $repository = $this->repositoryResolver->getRepositoryForEvent($event);

        if (!$repository) {
            return;
        }

        $vote = $repository->countByReferenceAndByIp($event->getReference(),  $event->getRequest()->getClientIp());

        if ($vote >= $this->maxNumber) {
             $event->stopPropagation();
        }
    }

}
