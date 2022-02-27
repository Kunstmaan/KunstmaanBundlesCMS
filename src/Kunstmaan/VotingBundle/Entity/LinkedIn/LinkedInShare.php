<?php

namespace Kunstmaan\VotingBundle\Entity\LinkedIn;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\VotingBundle\Entity\AbstractVote;
use Kunstmaan\VotingBundle\Repository\LinkedIn\LinkedInShareRepository;

/**
 * A LinkedIn Share Event
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\VotingBundle\Repository\LinkedIn\LinkedInShareRepository")
 * @ORM\Table(name="kuma_voting_linkedinshare")
 */
#[ORM\Entity(repositoryClass: LinkedInShareRepository::class)]
#[ORM\Table(name: 'kuma_voting_linkedinshare')]
class LinkedInShare extends AbstractVote
{
}
