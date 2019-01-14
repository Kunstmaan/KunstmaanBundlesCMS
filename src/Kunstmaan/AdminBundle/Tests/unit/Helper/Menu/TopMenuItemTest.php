<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Menu;

use Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder;
use Kunstmaan\AdminBundle\Helper\Menu\TopMenuItem;
use PHPUnit_Framework_TestCase;

/**
 * Class TopMenuItemTest
 */
class TopMenuItemTest extends PHPUnit_Framework_TestCase
{
    public function testGetSetRole()
    {
        /* @var $menuBuilder MenuBuilder */
        $menuBuilder = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Menu\MenuBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $object = new TopMenuItem($menuBuilder);
        $object->setAppearInSidebar(true);
        $this->assertTrue($object->getAppearInSidebar());
        $object->setAppearInSidebar(false);
        $this->assertFalse($object->getAppearInSidebar());
        $object->setAppearInSidebar(true);
        $this->assertTrue($object->getAppearInSidebar());
    }
}
