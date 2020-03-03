<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\EventListener\PasswordCheckListener;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\Translator;

class PasswordCheckListenerTest extends TestCase
{
    /**
     * @dataProvider requestDataProvider
     */
    public function testListener($uri, $shouldPerformCheck, $tokenStorageCallCount)
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => $uri]);
        $auth = $this->createMock(AuthorizationCheckerInterface::class);
        $storage = $this->createMock(TokenStorageInterface::class);
        $token = $this->createMock(UsernamePasswordToken::class);
        $user = $user = $this->createMock(User::class);
        $router = $this->createMock(RouterInterface::class);
        $session = $this->createMock(Session::class);
        $flash = $this->createMock(FlashBag::class);
        $trans = $this->createMock(Translator::class);
        $adminRouteHelper = $this->createMock(AdminRouteHelper::class);
        $event = $this->createMock(GetResponseEvent::class);

        $storage->expects($this->exactly($tokenStorageCallCount))->method('getToken')->willReturn($token);
        $event->expects($this->exactly($shouldPerformCheck ? 2 : 1))->method('getRequest')->willReturn($request);
        $auth->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('isGranted')->willReturn(true);
        $token->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('getUser')->willReturn($user);
        $user->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('isPasswordChanged')->willReturn(false);
        $router->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('generate')->willReturn(true);
        $session->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('getFlashBag')->willReturn($flash);
        $flash->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('add')->willReturn(true);
        $trans->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('trans')->willReturn(true);
        $adminRouteHelper->method('isAdminRoute')->willReturn($shouldPerformCheck);

        $listener = new PasswordCheckListener($auth, $storage, $router, $session, $trans, $adminRouteHelper);
        $listener->onKernelRequest($event);
    }

    public function requestDataProvider()
    {
        return [
            ['/en/admin/', true, 2],
            ['/en/random', false, 0],
            ['/en/admin/preview/', true, 2],
        ];
    }
}
