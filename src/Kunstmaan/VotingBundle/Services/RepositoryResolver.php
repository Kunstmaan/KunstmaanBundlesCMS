<?php

namespace Kunstmaan\VotingBundle\Services;

use Doctrine\ORM\EntityManager;

use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;

/**
 * Helper class get repository for an event
 */
class RepositoryResolver
{


	/**
	* Constructor
	* @param Object $em entity manager
	*/
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	* Return repository for event
	* @param Event $event event
	*
	* @return Repository
	*/
	public function getRepositoryForEvent($event)
	{
		$repository = null;

		if ($event instanceof DownVoteEvent) {
			$repository = $this->em->getRepository('Kunstmaan\VotingBundle\Entity\UpDown\DownVote');
		}

		if ($event instanceof UpVoteEvent) {
			$repository = $this->em->getRepository('Kunstmaan\VotingBundle\Entity\UpDown\UpVote');
		}

		if ($event instanceof LinkedInShareEvent) {
			$repository = $this->em->getRepository('Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare');
		}

		if ($event instanceof FacebookLikeEvent) {
			$repository = $this->em->getRepository('Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike');
		}

		if ($event instanceof FacebookLikeEvent) {
			$repository = $this->em->getRepository('Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend');
		}

		return $repository;
	}

}