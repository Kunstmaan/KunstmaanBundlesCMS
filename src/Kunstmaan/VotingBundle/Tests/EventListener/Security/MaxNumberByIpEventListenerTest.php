<?php

namespace Kunstmaan\VotingBundle\Tests\EventListener\Security;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent;
use Kunstmaan\VotingBundle\EventListener\Security\MaxNumberByIpEventListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Kunstmaan\VotingBundle\Repository\AbstractVoteRepository;
use Kunstmaan\VotingBundle\Services\RepositoryResolver;

class MaxNumberByIpEventListenerTest extends TestCase
{
    /**
     * @param int $voteNumber
     *
     * @return \Kunstmaan\VotingBundle\Services\RepositoryResolver
     */
    protected function mockRepositoryResolver($returnNull, $voteNumber = 0)
    {
        $mockedRepository = null;

        if (!$returnNull) {
            $mockedRepository = $this->createMock(AbstractVoteRepository::class);

            $mockedRepository->expects($this->any())
             ->method('countByReferenceAndByIp')
             ->willReturn($voteNumber);
        }

        $mockedResolver = $this->createMock(RepositoryResolver::class);

        $mockedResolver->expects($this->any())
             ->method('getRepositoryForEvent')
             ->willReturn($mockedRepository);

        /* @var \Kunstmaan\VotingBundle\Services\RepositoryResolver $mockedResolver */
        return $mockedResolver;
    }

    /**
     * @dataProvider dataTestOnVote
     */
    public function testOnVote($maxNumber, $number, $stopPropagation)
    {
        $resolver = $this->mockRepositoryResolver(false, $number);

        $event = new UpVoteEvent(new Request(), 'xxx', 1);
        $listener = new MaxNumberByIpEventListener($resolver, $maxNumber);

        $listener->onVote($event);
        $this->assertSame($stopPropagation, $event->isPropagationStopped());
    }

    /**
     * @dataProvider dataTestOnVote
     */
    public function testOnVoteReturnsNothing($maxNumber, $number, $stopPropagation)
    {
        $event = new FacebookLikeEvent(new Request(), 'xxx', 2);

        $resolver = $this->mockRepositoryResolver(false, $number);

        $listener = new MaxNumberByIpEventListener($resolver, $maxNumber);

        $listener->onVote($event);
        $this->assertSame($stopPropagation, $event->isPropagationStopped());
    }

    /**
     * Data for test on vote
     *
     * @return array
     */
    public function dataTestOnVote()
    {
        return [
            [2, 2, true],
            [2, 1, false],
            [2, 3, true],
        ];
    }
}
