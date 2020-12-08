<?php

namespace Kunstmaan\UtilitiesBundle\Tests\Helper;

use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ClassLookupTest extends TestCase
{
    public function testGetClass()
    {
        $class = ClassLookup::getClass(new Response());
        $this->assertEquals(Response::class, $class);
    }

    public function testGetClassName()
    {
        $class = ClassLookup::getClassName(new Response());
        $this->assertEquals('Response', $class);
    }
}
