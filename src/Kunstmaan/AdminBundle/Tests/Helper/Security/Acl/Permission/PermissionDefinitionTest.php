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

    protected function setUp(): void
    {
        $this->object = new PermissionDefinition(['VIEW']);
    }

    public function testConstructThrowsExceptionWithInvalidParameters()
    {
        $this->expectException(\InvalidArgumentException::class);
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

    public function testSetPermissionsWithInvalidData()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->object->setPermissions([]);
    }
}
