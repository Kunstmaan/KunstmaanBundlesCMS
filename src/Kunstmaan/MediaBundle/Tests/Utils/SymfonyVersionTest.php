<?php

namespace Kunstmaan\MediaBundle\Tests\Utils;

use Kunstmaan\MediaBundle\Utils\SymfonyVersion;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @covers \Kunstmaan\MediaBundle\Utils\SymfonyVersion
 */
class SymfonyVersionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetRootWebPath()
    {
        $path = SymfonyVersion::getRootWebPath();
        $this->assertStringStartsWith('%kernel.project_dir%/', $path);
        $this->assertStringEndsWith(Kernel::VERSION_ID < 40000 ? 'web' : 'public', $path);
    }

    public function testIsKernelLessThan()
    {
        $this->assertTrue(SymfonyVersion::isKernelLessThan(100, 100, 100));
        $this->assertFalse(SymfonyVersion::isKernelLessThan(1, 1, 1));
    }
}
