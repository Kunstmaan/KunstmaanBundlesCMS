<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\Entity\DeepCloneInterface;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\AdminBundle\EventListener\CloneListener;
use Kunstmaan\MediaBundle\Entity\Media;
use PHPUnit_Framework_TestCase;

class CloneListenerTest extends PHPUnit_Framework_TestCase
{
    public function testListener()
    {
        $listener = new CloneListener();
        $user = new Media();
        $user->setId(666);
        $dc = $this->createMock(DeepCloneInterface::class);
        $dc->expects($this->once())->method('deepClone')->willReturn(true);
        $event = $this->createMock(DeepCloneAndSaveEvent::class);
        $event->expects($this->any())->method('getClonedEntity')->willReturn($user);

        $listener->onDeepCloneAndSave($event);
        $this->assertNull($user->getId());

        $event = $this->createMock(DeepCloneAndSaveEvent::class);
        $event->expects($this->any())->method('getClonedEntity')->willReturn($dc);

        $listener->onDeepCloneAndSave($event);
    }
}
