<?php

namespace Kunstmaan\AdminListBundle\Tests\Helper;

use Kunstmaan\AdminBundle\Helper\DomainConfiguration;
use PHPUnit\Framework\TestCase;

class DomainConfigurationTest extends TestCase
{
    /**
     * @var DomainConfiguration
     */
    protected $object;

    public function setUp(): void
    {
        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $this->object = new DomainConfiguration($requestStack, true, 'nl', 'nl|fr|en');
    }

    public function testGetSet()
    {
        $data = $this->object->getLocalesExtraData();
        $this->assertCount(0, $data);
        $data = $this->object->getFullHostConfig();
        $this->assertCount(0, $data);
        $this->assertNull($this->object->getFullHost());
        $this->assertNull($this->object->getFullHostById(123));
        $this->assertNull($this->object->getHostSwitched());
        $this->assertNull($this->object->getHostBaseUrl());
    }
}
