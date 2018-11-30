<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use DateTime;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersionLock;
use PHPUnit_Framework_TestCase;

/**
 * Class NodeVersionLockTest
 */
class NodeVersionLockTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $nodeVersionLock = new NodeVersionLock();

        $nodeVersionLock->setCreatedAt(new DateTime());
        $this->assertInstanceOf(DateTime::class, $nodeVersionLock->getCreatedAt());

        $nodeVersionLock->setPublicVersion(true);
        $this->assertTrue($nodeVersionLock->isPublicVersion());
        $nodeVersionLock->setPublicVersion(false);
        $this->assertFalse($nodeVersionLock->isPublicVersion());

        $nodeVersionLock->setOwner('delboy1978uk');
        $this->assertEquals('delboy1978uk', $nodeVersionLock->getOwner());

        $nodeVersionLock->setNodeTranslation(new NodeTranslation());
        $this->assertInstanceOf(NodeTranslation::class, $nodeVersionLock->getNodeTranslation());
    }
}
