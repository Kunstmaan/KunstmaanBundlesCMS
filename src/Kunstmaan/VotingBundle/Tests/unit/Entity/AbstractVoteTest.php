<?php

namespace Kunstmaan\VotingBundle\Tests\Entity;

use DateTime;
use Kunstmaan\VotingBundle\Entity\Facebook\FacebookLike;
use PHPUnit_Framework_TestCase;

/**
 * Class AbstractVoteTest
 * @package Tests\Kunstmaan\TaggingBundle\Entity
 */
class AbstractVoteTest extends PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $object = new FacebookLike();
        $time = new DateTime();

        $object->setId(666);
        $object->setValue(999);
        $object->setIp('8.8.8.8');
        $object->setMeta('something');
        $object->setReference('ref');
        $object->setTimestamp($time);

        $this->assertEquals(666, $object->getId());
        $this->assertEquals(999, $object->getValue());
        $this->assertEquals('8.8.8.8', $object->getIp());
        $this->assertEquals('something', $object->getMeta());
        $this->assertEquals('ref', $object->getReference());
        $this->assertInstanceOf(DateTime::class, $object->getTimestamp());

        $object = new FacebookLike();
        $object->prePersist();
        $this->assertInstanceOf(DateTime::class, $object->getTimestamp());
        $this->assertEquals(FacebookLike::DEFAULT_VALUE, $object->getValue());

    }
}
