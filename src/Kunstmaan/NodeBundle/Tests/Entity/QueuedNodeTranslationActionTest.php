<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\QueuedNodeTranslationAction;
use PHPUnit\Framework\TestCase;

class QueuedNodeTranslationActionTest extends TestCase
{
    public function testGetSet()
    {
        $queuedNodeTranslationAction = new QueuedNodeTranslationAction();
        $user = new User();

        $queuedNodeTranslationAction->setDate(new \DateTime());
        $this->assertInstanceOf(\DateTime::class, $queuedNodeTranslationAction->getDate());

        $queuedNodeTranslationAction->setAction('some-action');
        $this->assertEquals('some-action', $queuedNodeTranslationAction->getAction());

        $queuedNodeTranslationAction->setUser($user);
        $this->assertInstanceOf(User::class, $queuedNodeTranslationAction->getUser());

        $queuedNodeTranslationAction->setNodeTranslation(new NodeTranslation());
        $this->assertInstanceOf(NodeTranslation::class, $queuedNodeTranslationAction->getNodeTranslation());
    }
}
