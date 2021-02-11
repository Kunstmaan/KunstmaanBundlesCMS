<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\AdminBundle\Entity\DeepCloneInterface;
use Kunstmaan\AdminBundle\Event\DeepCloneAndSaveEvent;
use Kunstmaan\AdminBundle\EventListener\CloneListener;
use PHPUnit\Framework\TestCase;

class CloneListenerTest extends TestCase
{
    public function testOnDeepCloneAndSaveWithAbstractEntity()
    {
        $listener = new CloneListener();

        /** @var AbstractEntity $entityMock */
        $entityMock = $this->getMockForAbstractClass(AbstractEntity::class);
        $entityMock->setId(747);

        $event = $this->createMock(DeepCloneAndSaveEvent::class);
        $event->expects($this->any())->method('getClonedEntity')->willReturn($entityMock);

        $listener->onDeepCloneAndSave($event);
        $this->assertNull($entityMock->getId());
    }

    public function testOnDeepCloneAndSaveWithDeepCloneInterface()
    {
        $listener = new CloneListener();

        $deepCloneInterfaceMock = $this->createMock(DeepCloneInterface::class);
        $deepCloneInterfaceMock->expects($this->once())->method('deepClone');

        $event = $this->createMock(DeepCloneAndSaveEvent::class);
        $event->expects($this->any())->method('getClonedEntity')->willReturn($deepCloneInterfaceMock);

        $listener->onDeepCloneAndSave($event);
    }
}
