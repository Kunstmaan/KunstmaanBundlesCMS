<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl\Permission;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionDefinition;
use PHPUnit\Framework\TestCase;

class PermissionDefinitionTest extends TestCase
{
    /**
     * @var PermissionDefinition
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp(): void
    {
        $this->object = new PermissionDefinition(array('VIEW'));
    }

    public function test__constructThrowsExceptionWithInvalidParameters()
    {
        $this->expectException(\InvalidArgumentException::class);
        new PermissionDefinition(array(), null, null);
    }

    public function testSetGetAlias()
    {
        $this->object->setAlias('alias');
        $this->assertEquals('alias', $this->object->getAlias());
    }

    public function testSetGetEntity()
    {
        $this->object->setEntity('entity');
        $this->assertEquals('entity', $this->object->getEntity());
    }

    public function testSetGetPermissions()
    {
        $this->object->setPermissions(array('EDIT', 'VIEW', 'DELETE'));
        $this->assertEquals(array('EDIT', 'VIEW', 'DELETE'), $this->object->getPermissions());
    }

    public function testSetPermissionsWithInvalidData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->object->setPermissions(array());
    }
}
