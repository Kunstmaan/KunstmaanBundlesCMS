<?php

namespace Kunstmaan\ConfigBundle\Tests\Entity;

use Kunstmaan\ConfigBundle\Entity\AbstractConfig;
use PHPUnit\Framework\TestCase;

class Config extends AbstractConfig
{
    public function getDefaultAdminType()
    {
        return null;
    }

    public function getInternalName()
    {
        return null;
    }

    public function getLabel()
    {
        return null;
    }
}

class AbstractConfigTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new Config();

        $this->assertIsArray($entity->getRoles());
        $this->assertEquals('ROLE_SUPER_ADMIN', $entity->getRoles()[0]);
    }
}
