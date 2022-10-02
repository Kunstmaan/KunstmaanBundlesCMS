<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\AdminPanel;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAction;
use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelLogoutAction;
use Kunstmaan\AdminBundle\Helper\AdminPanel\DefaultAdminPanelAdaptor;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Bundle\SecurityBundle\Security\FirewallConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

class DefaultAdminPanelAdapterTest extends TestCase
{
    use ExpectDeprecationTrait;

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

    /**
     * @group legacy
     */
    public function testAdminPanelAdapterConstructorDeprecation()
    {
        $this->expectDeprecation('Since kunstmaan/admin-bundle 6.2: Not passing a value for "$logoutUrlGenerator" in "Kunstmaan\AdminBundle\Helper\AdminPanel\DefaultAdminPanelAdaptor::__construct" is deprecated and will be required in 7.0.');

        $storage = new TokenStorage();
        if (method_exists(FirewallConfig::class, 'getAuthenticators')) {
            $token = new UsernamePasswordToken((new User())->setUsername('test'), 'main');
        } else {
            $token = new UsernamePasswordToken((new User())->setUsername('test'), 'password', 'main');
        }
        $storage->setToken($token);

        $adapter = new DefaultAdminPanelAdaptor($storage);
        $actions = $adapter->getAdminPanelActions();

        $this->assertCount(3, $actions);
        $this->assertInstanceOf(AdminPanelAction::class, $actions[0]);
        $this->assertInstanceOf(AdminPanelAction::class, $actions[1]);
        $this->assertInstanceOf(AdminPanelAction::class, $actions[2]);
    }
}
