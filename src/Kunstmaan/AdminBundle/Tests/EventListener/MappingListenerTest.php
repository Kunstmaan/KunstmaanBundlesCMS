<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use Kunstmaan\AdminBundle\Entity\AclChangeset;
use Kunstmaan\AdminBundle\EventListener\MappingListener;
use PHPUnit\Framework\TestCase;

class MappingListenerTest extends TestCase
{
    public function testListener()
    {
        $args = $this->createMock(LoadClassMetadataEventArgs::class);
        $meta = $this->createMock(ClassMetadata::class);

        $meta->table = ['name' => 'test_table'];

        $args->expects($this->once())->method('getClassMetadata')->willReturn($meta);
        $meta->expects($this->once())->method('getName')->willReturn(AclChangeset::class);
        $meta->expects($this->once())->method('mapManyToOne')->willReturn(AclChangeset::class);
        $meta->expects($this->once())->method('mapManyToMany')->willReturn(AclChangeset::class);

        $listener = new MappingListener(AclChangeset::class);
        $listener->loadClassMetadata($args);
    }
}
