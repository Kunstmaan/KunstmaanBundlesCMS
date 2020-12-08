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

    protected function setUp()
    {
        $this->object = new PermissionDefinition(['VIEW']);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructThrowsExceptionWithInvalidParameters()
    {
        new PermissionDefinition([], null, null);
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
        $this->object->setPermissions(['EDIT', 'VIEW', 'DELETE']);
        $this->assertEquals(['EDIT', 'VIEW', 'DELETE'], $this->object->getPermissions());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetPermissionsWithInvalidData()
    {
        $this->object->setPermissions([]);
    }
}
