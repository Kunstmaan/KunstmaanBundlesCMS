<?php

namespace Kunstmaan\VotingBundle\Tests\EventListener\Security;

use Kunstmaan\VotingBundle\EventListener\Security\MaxNumberByIpEventListener;
use Symfony\Component\HttpFoundation\Request;

/**
 * Test Max Number by Ip event listener
 */
class MaxNumberByIpEventListenerTest extends \PHPUnit_Framework_TestCase
{
    protected function mockRepositoryResolver($returnNull, $voteNumber = 0)
    {
        $mockedRepository = null;

        if (!$returnNull) {
            $mockedRepository = $this->getMock('Kunstmaan\VotingBundle\Repository\AbstractVoteRepository', array('countByReferenceAndByIp'), array(), 'MockedRepository', false);

            $mockedRepository->expects($this->any())
             ->method('countByReferenceAndByIp')
             ->will($this->returnValue($voteNumber));
        }

        $mockedResolver = $this->getMock('Kunstmaan\VotingBundle\Services\RepositoryResolver', array('getRepositoryForEvent'), array(), 'MockedResolver', false);

        $mockedResolver->expects($this->any())
             ->method('getRepositoryForEvent')
             ->will($this->returnValue($mockedRepository));

        return $mockedResolver;
    }

    /**
     * @dataProvider dataTestOnVote
     */
    public function testOnVote($maxNumber, $number, $stopPropagation)
    {
        $mockedEvent = $this->getMock('Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent', array('stopPropagation'), array(new Request(), null, null));

        if ($stopPropagation) {
            $mockedEvent->expects($this->once())
                ->method('stopPropagation');
        } else {
            $mockedEvent->expects($this->never())
                ->method('stopPropagation');
        }

        $resolver = $this->mockRepositoryResolver(false, $number);

        $listener = new MaxNumberByIpEventListener($resolver, $maxNumber);

        $listener->onVote($mockedEvent);
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
