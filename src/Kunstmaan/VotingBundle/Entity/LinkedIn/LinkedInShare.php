<?php

namespace Kunstmaan\VotingBundle\Entity\LinkedIn;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\VotingBundle\Entity\AbstractVote;

/**
 * A LinkedIn Share Event
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\VotingBundle\Repository\LinkedIn\LinkedInShareRepository")
 * @ORM\Table(name="kuma_voting_linkedinshare")
 */
class LinkedInShare extends AbstractVote
{

}
