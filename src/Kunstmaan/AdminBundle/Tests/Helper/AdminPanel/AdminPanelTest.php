<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\AdminPanel;

use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanel;
use Kunstmaan\AdminBundle\Helper\AdminPanel\DefaultAdminPanelAdaptor;
use PHPUnit\Framework\TestCase;

class AdminPanelTest extends TestCase
{
    public function testAdminPanel()
    {
        $adapter = $this->createMock(DefaultAdminPanelAdaptor::class);
        $adapter->expects($this->once())->method('getAdminPanelActions')->willReturn([]);
        $panel = new AdminPanel();
        $panel->addAdminPanelAdaptor($adapter);
        $actions = $panel->getAdminPanelActions();
        $this->assertEmpty($actions);
    }
}
