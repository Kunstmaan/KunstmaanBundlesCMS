<?php

namespace Kunstmaan\VotingBundle\Tests\Services;

use Kunstmaan\VotingBundle\Services\RepositoryResolver;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for repository resolver
 */
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
        return array(
            array($this->createMock('\Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent'), 'Kunstmaan\VotingBundle\Entity\UpDown\DownVote'),
            array($this->createMock('\Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent'), 'Kunstmaan\VotingBundle\Entity\UpDown\UpVote'),
            array($this->createMock('\Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent'), 'Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike'),
            array($this->createMock('\Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent'), 'Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend'),
            array($this->createMock('\Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent'), 'Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare'),
        );
    }
}
