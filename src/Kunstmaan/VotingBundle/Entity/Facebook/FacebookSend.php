<?php

namespace Kunstmaan\VotingBundle\Entity\Facebook;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\VotingBundle\Entity\AbstractVote;

/**
 * A Facebook Send Event
 *
 * The reference will hold the URL that has been send
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\VotingBundle\Repository\Facebook\FacebookSendRepository")
 * @ORM\Table(name="kuma_voting_facebooksend")
 */
class FacebookSend extends AbstractVote
{
}
