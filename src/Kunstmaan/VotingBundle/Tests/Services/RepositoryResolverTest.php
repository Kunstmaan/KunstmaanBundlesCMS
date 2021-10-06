<?php

namespace Kunstmaan\VotingBundle\Tests\Services;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Kunstmaan\VotingBundle\Services\RepositoryResolver;
use PHPUnit\Framework\TestCase;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Symfony\Component\HttpFoundation\Request;
use Kunstmaan\VotingBundle\Entity\UpDown\DownVote;
use Kunstmaan\VotingBundle\Entity\UpDown\UpVote;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend;
use Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare;

class RepositoryResolverTest extends TestCase
{
    /**
     * @dataProvider dataRepositoryEvent
     */
    public function testGetRepositoryForEvent($event, $repositoryname)
    {
        $mockEm = $this->createMock('Doctrine\ORM\EntityManager');

        $mockEm->expects($this->once())
                 ->method('getRepository')
                 ->with($this->equalTo($repositoryname));

        $resolver = new RepositoryResolver($mockEm);

        $resolver->getRepositoryForEvent($event);
    }

    public function dataRepositoryEvent()
    {
        $request = new Request();

        return [
            [new DownVoteEvent($request, 'xxx', 1), DownVote::class],
            [new UpVoteEvent($request, 'xxx', 1), UpVote::class],
            [new FacebookLikeEvent($request, 'xxx', 1), FacebookLike::class],
            [new FacebookSendEvent($request, 'xxx', 1), FacebookSend::class],
            [new LinkedInShareEvent($request, 'xxx', 1), LinkedInShare::class],
        ];
    }
}
