<?php

namespace Tests\Kunstmaan\UtilitiesBundle\Helper;

use Kunstmaan\UtilitiesBundle\Helper\ClassLookup;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-12 at 11:16:18.
 */
class ClassLookupTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClass()
    {
        $class = ClassLookup::getClass(new DummyClass());
        $this->assertEquals('Tests\Kunstmaan\UtilitiesBundle\Helper\DummyClass', $class);
    }

    public function testGetClassName()
    {
        $class = ClassLookup::getClassName(new DummyClass());
        $this->assertEquals('DummyClass', $class);
    }

}
