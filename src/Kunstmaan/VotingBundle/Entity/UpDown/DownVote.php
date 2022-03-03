<?php

namespace Kunstmaan\VotingBundle\Entity\UpDown;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\VotingBundle\Entity\AbstractVote;
use Kunstmaan\VotingBundle\Repository\UpDown\DownVoteRepository;

/**
 * A standard up vote
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\VotingBundle\Repository\UpDown\DownVoteRepository")
 * @ORM\Table(name="kuma_voting_downvote")
 */
#[ORM\Entity(repositoryClass: DownVoteRepository::class)]
#[ORM\Table(name: 'kuma_voting_downvote')]
class DownVote extends AbstractVote
{
}
