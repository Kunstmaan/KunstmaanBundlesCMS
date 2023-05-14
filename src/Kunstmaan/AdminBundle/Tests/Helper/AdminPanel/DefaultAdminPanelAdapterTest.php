<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\AdminPanel;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAction;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelLogoutAction;
use Kunstmaan\AdminBundle\Helper\AdminPanel\DefaultAdminPanelAdaptor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

class DefaultAdminPanelAdapterTest extends TestCase
{
    public function testAdminPanelAdapter()
    {
        $requestStack = new RequestStack();
        $requestStack->push(new Request());

        $storage = new TokenStorage();
        $token = new UsernamePasswordToken((new User())->setUsername('test'), 'main');
        $storage->setToken($token);

        $logoutUrlGenerator = new LogoutUrlGenerator($requestStack, null, $storage);
        $logoutUrlGenerator->registerListener('main', '/logout', 'logout', '_token');

        $adapter = new DefaultAdminPanelAdaptor($storage, $logoutUrlGenerator);
        $actions = $adapter->getAdminPanelActions();

        $this->assertCount(3, $actions);
        $this->assertInstanceOf(AdminPanelAction::class, $actions[0]);
        $this->assertInstanceOf(AdminPanelAction::class, $actions[1]);
        $this->assertInstanceOf(AdminPanelLogoutAction::class, $actions[2]);
    }
}
