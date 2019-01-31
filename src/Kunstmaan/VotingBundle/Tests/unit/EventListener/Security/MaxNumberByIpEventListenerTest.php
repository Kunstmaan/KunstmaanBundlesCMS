<?php

namespace Kunstmaan\VotingBundle\Tests\EventListener\Security;

use Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent;
use Kunstmaan\VotingBundle\EventListener\Security\MaxNumberByIpEventListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Test Max Number by Ip event listener
 */
class MaxNumberByIpEventListenerTest extends TestCase
{
    /**
     * @param $returnNull
     * @param int $voteNumber
     *
     * @return \Kunstmaan\VotingBundle\Services\RepositoryResolver
     */
    protected function mockRepositoryResolver($returnNull, $voteNumber = 0)
    {
        $mockedRepository = null;

        if (!$returnNull) {
            $mockedRepository = $this->createMock('Kunstmaan\VotingBundle\Repository\AbstractVoteRepository'); //, array('countByReferenceAndByIp'), array(), 'MockedRepository', false);

            $mockedRepository->expects($this->any())
             ->method('countByReferenceAndByIp')
             ->will($this->returnValue($voteNumber));
        }

        $mockedResolver = $this->createMock('Kunstmaan\VotingBundle\Services\RepositoryResolver'); //, array('getRepositoryForEvent'), array(), 'MockedResolver', false);

        $mockedResolver->expects($this->any())
             ->method('getRepositoryForEvent')
             ->will($this->returnValue($mockedRepository));

        /* @var \Kunstmaan\VotingBundle\Services\RepositoryResolver $mockedResolver */
        return $mockedResolver;
    }

    /**
     * @dataProvider dataTestOnVote
     */
    public function testOnVote($maxNumber, $number, $stopPropagation)
    {
        $mockedEvent = $this->createMock('Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent'); //, array('stopPropagation'), array(new Request(), null, null));

        if ($stopPropagation) {
            $mockedEvent->expects($this->once())
                ->method('stopPropagation');
        } else {
            $mockedEvent->expects($this->never())
                ->method('stopPropagation');
        }

        $mockedEvent->expects($this->any())
            ->method('getRequest')
            ->willReturn(new Request());

        $resolver = $this->mockRepositoryResolver(false, $number);

        $listener = new MaxNumberByIpEventListener($resolver, $maxNumber);

        $listener->onVote($mockedEvent);
    }

    /**
     * @dataProvider dataTestOnVote
     */
    public function testOnVoteReturnsNothing($maxNumber, $number, $stopPropagation)
    {
        $event = new FacebookLikeEvent(new Request(), new Response(), 2);

        $resolver = $this->mockRepositoryResolver(false, $number);

        $listener = new MaxNumberByIpEventListener($resolver, $maxNumber);

        $this->assertNull($listener->onVote($event));
    }

    /**
     * Data for test on vote
     *
     * @return array
     */
    public function dataTestOnVote()
    {
        return array(
            array(2, 2, true),
            array(2, 1, false),
            array(2, 3, true),
        );
    }
}
