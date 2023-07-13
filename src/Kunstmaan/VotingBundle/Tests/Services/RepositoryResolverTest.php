<?php

namespace Kunstmaan\VotingBundle\Tests\Services;

use Doctrine\ORM\EntityManager;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend;
use Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare;
use Kunstmaan\VotingBundle\Entity\UpDown\DownVote;
use Kunstmaan\VotingBundle\Entity\UpDown\UpVote;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent;
use Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent;
use Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Kunstmaan\VotingBundle\Services\RepositoryResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class RepositoryResolverTest extends TestCase
{
    /**
     * @dataProvider dataRepositoryEvent
     */
    public function testGetRepositoryForEvent($event, $repositoryname)
    {
        $mockEm = $this->createMock(EntityManager::class);

        $mockEm->expects($this->once())
                 ->method('getRepository')
                 ->with($this->equalTo($repositoryname));

        $resolver = new RepositoryResolver($mockEm);

        $resolver->getRepositoryForEvent($event);
    }

    public function dataRepositoryEvent(): \Iterator
    {
        $request = new Request();
        yield [new DownVoteEvent($request, 'xxx', 1), DownVote::class];
        yield [new UpVoteEvent($request, 'xxx', 1), UpVote::class];
        yield [new FacebookLikeEvent($request, 'xxx', 1), FacebookLike::class];
        yield [new FacebookSendEvent($request, 'xxx', 1), FacebookSend::class];
        yield [new LinkedInShareEvent($request, 'xxx', 1), LinkedInShare::class];
    }
}
