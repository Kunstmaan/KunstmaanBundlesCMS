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
    protected function setUp()
    {
        $this->object = new PermissionDefinition(array('VIEW'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function test__constructThrowsExceptionWithInvalidParameters()
    {
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPermissionsWithInvalidData()
    {
        $this->object->setPermissions(array());
    }
}
