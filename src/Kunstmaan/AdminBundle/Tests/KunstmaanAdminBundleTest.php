<?php

namespace Kunstmaan\AdminBundle\Tests;

use Kunstmaan\AdminBundle\DependencyInjection\KunstmaanAdminExtension;
use Kunstmaan\AdminBundle\KunstmaanAdminBundle;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class KunstmaanAdminBundleTest extends PHPUnit_Framework_TestCase
{
    public function testBundle()
    {
        $containerBuilder = new ContainerBuilder();
        $bundle = new KunstmaanAdminBundle();
        $bundle->build($containerBuilder);
        $resources = $containerBuilder->getResources();
        $extensions = $containerBuilder->getExtensions();

        $this->assertCount(7, $resources);
        $this->assertCount(1, $extensions);
        $this->assertArrayHasKey('kunstmaan_admin', $extensions);
        $this->assertInstanceOf(KunstmaanAdminExtension::class, $extensions['kunstmaan_admin']);
        $this->assertInstanceOf(FileResource::class, $resources[1]);
        $this->assertInstanceOf(FileResource::class, $resources[2]);
        $this->assertInstanceOf(FileResource::class, $resources[3]);
    }
}
