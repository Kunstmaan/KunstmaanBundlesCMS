<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\EventListener\PasswordCheckListener;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\Translator;

class PasswordCheckListenerTest extends PHPUnit_Framework_TestCase
{
    public function testListener()
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/en/admin/']);
        $auth = $this->createMock(AuthorizationCheckerInterface::class);
        $storage = $this->createMock(TokenStorageInterface::class);
        $token = $this->createMock(UsernamePasswordToken::class);
        $user = $user = $this->createMock(User::class);
        $router = $this->createMock(Router::class);
        $session = $this->createMock(Session::class);
        $flash = $this->createMock(FlashBag::class);
        $trans = $this->createMock(Translator::class);
        $adminRouteHelper = $this->createMock(AdminRouteHelper::class);
        $event = $this->createMock(GetResponseEvent::class);

        $event->expects($this->exactly(2))->method('getRequest')->willReturn($request);
        $storage->expects($this->exactly(4))->method('getToken')->willReturn($token);
        $auth->expects($this->exactly(1))->method('isGranted')->willReturn(true);
        $token->expects($this->exactly(1))->method('getUser')->willReturn($user);
        $user->expects($this->exactly(1))->method('isPasswordChanged')->willReturn(false);
        $router->expects($this->exactly(1))->method('generate')->willReturn(true);
        $session->expects($this->exactly(1))->method('getFlashBag')->willReturn($flash);
        $flash->expects($this->exactly(1))->method('add')->willReturn(true);
        $trans->expects($this->exactly(1))->method('trans')->willReturn(true);
        $adminRouteHelper->method('isAdminRoute')->will($this->returnValueMap([
            ['/en/admin/', true],
            ['/en/random', false],
            ['/en/admin/preview/', false],
        ]));

        $listener = new PasswordCheckListener($auth, $storage, $router, $session, $trans, $adminRouteHelper);
        $listener->onKernelRequest($event);

        $request = $request->duplicate([], [], [], [], [], ['REQUEST_URI' => '/en/random']);
        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->any())->method('getRequest')->willReturn($request);

        $listener->onKernelRequest($event);

        $request = $request->duplicate([], [], [], [], [], ['REQUEST_URI' => '/en/admin/preview/']);
        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->any())->method('getRequest')->willReturn($request);

        $listener->onKernelRequest($event);
    }
}
