<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Permission;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\MaskBuilder;

/**
 * PermissionMapTest.
 */
class PermissionMapTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap::getMasks
     */
    public function testGetMasksReturnsNullWhenNotSupportedMask()
    {
        $map = new PermissionMap();
        $this->assertNull($map->getMasks('IS_AUTHENTICATED_REMEMBERED', null));
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap::getMasks
     */
    public function testGetMasks()
    {
        $map = new PermissionMap();
        $mask = $map->getMasks(PermissionMap::PERMISSION_DELETE, null);

        $this->assertEquals(array(MaskBuilder::MASK_DELETE), $mask);
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap::contains
     */
    public function testContains()
    {
        $map = new PermissionMap();

        $this->assertEquals(true, $map->contains('DELETE'));
        $this->assertEquals(false, $map->contains('DUMMY'));
    }

    /**
     * @covers Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap::getPossiblePermissions
     */
    public function testGetPossiblePermissions()
    {
        $map = new PermissionMap();

        $this->assertEquals(array('VIEW', 'EDIT', 'DELETE', 'PUBLISH', 'UNPUBLISH'), $map->getPossiblePermissions());
    }
}
