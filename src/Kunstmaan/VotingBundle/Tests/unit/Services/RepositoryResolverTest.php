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
        return [
            [$this->createMock('\Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent'), 'Kunstmaan\VotingBundle\Entity\UpDown\DownVote'],
            [$this->createMock('\Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent'), 'Kunstmaan\VotingBundle\Entity\UpDown\UpVote'],
            [$this->createMock('\Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent'), 'Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike'],
            [$this->createMock('\Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent'), 'Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend'],
            [$this->createMock('\Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent'), 'Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare'],
        ];
    }
}
