<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Permission;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use PHPUnit\Framework\TestCase;

/**
 * PermissionMapTest
 */
class PermissionMapTest extends TestCase
{
    public function testGetMasksReturnsNullWhenNotSupportedMask()
    {
        $map = new PermissionMap();
        $this->assertNull($map->getMasks('IS_AUTHENTICATED_REMEMBERED', null));
    }

    public function testGetMasks()
    {
        $map = new PermissionMap();
        $mask = $map->getMasks(PermissionMap::PERMISSION_DELETE, null);

        $this->assertEquals([MaskBuilder::MASK_DELETE], $mask);
    }

    public function testContains()
    {
        $map = new PermissionMap();

        $this->assertEquals(true, $map->contains('DELETE'));
        $this->assertEquals(false, $map->contains('DUMMY'));
    }

    public function testGetPossiblePermissions()
    {
        $map = new PermissionMap();

        $this->assertEquals(['VIEW', 'EDIT', 'DELETE', 'PUBLISH', 'UNPUBLISH'], $map->getPossiblePermissions());
    }

    public function testGetMaskBuilder()
    {
        $map = new PermissionMap();
        $this->assertInstanceOf(MaskBuilder::class, $map->getMaskBuilder());
    }
}
