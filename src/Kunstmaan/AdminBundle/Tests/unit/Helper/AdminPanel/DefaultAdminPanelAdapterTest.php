<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\AdminPanel;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAction;
use Kunstmaan\AdminBundle\Helper\AdminPanel\DefaultAdminPanelAdaptor;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class DefaultAdminPanelAdapterTest
 */
class DefaultAdminPanelAdapterTest extends PHPUnit_Framework_TestCase
{
    public function testAdminPanelAdapter()
    {
        $token = $this->createMock(TokenInterface::class);
        $storage = $this->createMock(TokenStorageInterface::class);
        $storage->expects($this->once())->method('getToken')->willReturn($token);
        $token->expects($this->once())->method('getUser')->willReturn(new User());
        $adapter = new DefaultAdminPanelAdaptor($storage);
        $actions = $adapter->getAdminPanelActions();

        $this->assertCount(3, $actions);
        $this->assertInstanceOf(AdminPanelAction::class, $actions[0]);
        $this->assertInstanceOf(AdminPanelAction::class, $actions[1]);
        $this->assertInstanceOf(AdminPanelAction::class, $actions[2]);
    }
}
