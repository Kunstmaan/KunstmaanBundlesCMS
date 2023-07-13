<?php

namespace Kunstmaan\MenuBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\MenuBundle\Entity\BaseMenu;
use Kunstmaan\MenuBundle\Entity\MenuItem;
use PHPUnit\Framework\TestCase;

class BaseMenuTest extends TestCase
{
    public function testGetSet()
    {
        $menu = new BaseMenu();
        $item = new MenuItem();

        $menu->setLocale('en');
        $menu->setId(5);
        $menu->setName('Vladimir Putin');
        $menu->setItems(new ArrayCollection([$item]));

        $this->assertSame('en', $menu->getLocale());
        $this->assertSame(5, $menu->getId());
        $this->assertSame('Vladimir Putin', $menu->getName());
        $this->assertInstanceOf(ArrayCollection::class, $menu->getItems());
        $this->assertCount(1, $menu->getItems());

        $item2 = clone $item;
        $menu->addItem($item2);
        $this->assertCount(2, $menu->getItems());
        $menu->removeItem($item2);
        $this->assertCount(1, $menu->getItems());
    }
}
