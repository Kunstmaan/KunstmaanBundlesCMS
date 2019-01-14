<?php

namespace Kunstmaan\NodeBundle\Tests\Event;

use Knp\Menu\MenuFactory;
use Knp\Menu\MenuItem;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\ConfigureActionMenuEvent;
use PHPUnit_Framework_TestCase;

/**
 * Class ConfigureActionMenuEventTest
 */
class ConfigureActionMenuEventTest extends PHPUnit_Framework_TestCase
{
    public function testGetSet()
    {
        $factory = new MenuFactory();
        $menu = new MenuItem('whatever', $factory);
        /** @var NodeVersion $nodeVersion */
        $nodeVersion = $this->createMock(NodeVersion::class);

        $event = new ConfigureActionMenuEvent($factory, $menu, $nodeVersion);

        $this->assertInstanceOf(MenuFactory::class, $event->getFactory());
        $this->assertInstanceOf(MenuItem::class, $event->getMenu());
        $this->assertInstanceOf(NodeVersion::class, $event->getActiveNodeVersion());
    }
}
