<?php

namespace Kunstmaan\VotingBundle\Tests\Services;

use Kunstmaan\VotingBundle\Services\RepositoryResolver;

/**
* Unit test for repository resolver
*/
class RepositoryResolverTest extends \PHPUnit_Framework_TestCase
{

    /**
    * @dataProvider dataRepositoryEvent
    */
    public function testGetRepositoryForEvent($event, $repositoryname)
    {

        $mockEm = $this->getMock('Doctrine\ORM\EntityManager', array('getRepository'), array() , 'MockedEm', false);

        $mockEm->expects($this->once())
                 ->method('getRepository')
                 ->with($this->equalTo($repositoryname));

        $resolver = new RepositoryResolver($mockEm);

        $resolver->getRepositoryForEvent($event);

    }

    public function dataRepositoryEvent()
    {
        return array(
            array($this->getMock('\Kunstmaan\VotingBundle\Event\UpDown\DownVoteEvent', array(), array() , 'MockDownVoteEvent', false), 'Kunstmaan\VotingBundle\Entity\UpDown\DownVote'),
            array($this->getMock('\Kunstmaan\VotingBundle\Event\UpDown\UpVoteEvent', array(), array() , 'MockUpVoteEvent', false), 'Kunstmaan\VotingBundle\Entity\UpDown\UpVote'),
            array($this->getMock('\Kunstmaan\VotingBundle\Event\Facebook\FacebookLikeEvent', array(), array() , 'MockFacebookLikeEvent', false), 'Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike'),
            array($this->getMock('\Kunstmaan\VotingBundle\Event\Facebook\FacebookSendEvent', array(), array() , 'MockFacebookSendEvent', false), 'Kunstmaan\VotingBundle\Entity\Facebook\FacebookSend'),
            array($this->getMock('\Kunstmaan\VotingBundle\Event\LinkedIn\LinkedInShareEvent', array(), array() , 'MockLinkedInShareEvent', false), 'Kunstmaan\VotingBundle\Entity\LinkedIn\LinkedInShare'),
        );
    }

}
