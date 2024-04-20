<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\EventListener\PasswordCheckListener;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\Translator;

class PasswordCheckListenerTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @dataProvider requestDataProvider
     */
    public function testListener($uri, $shouldPerformCheck, $tokenStorageCallCount)
    {
        $request = new Request([], [], ['_route' => 'example_route'], [], [], ['REQUEST_URI' => $uri]);
        $auth = $this->createMock(AuthorizationCheckerInterface::class);
        $storage = $this->createMock(TokenStorageInterface::class);
        $token = $this->createMock(UsernamePasswordToken::class);
        $user = $this->createMock(User::class);
        $router = $this->createMock(RouterInterface::class);
        $session = $this->createMock(Session::class);
        $flash = new FlashBag();
        $trans = $this->createMock(Translator::class);
        $adminRouteHelper = $this->createMock(AdminRouteHelper::class);
        $kernel = $this->createMock(KernelInterface::class);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $storage->expects($this->exactly($tokenStorageCallCount))->method('getToken')->willReturn($token);
        $auth->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('isGranted')->willReturn(true);
        $token->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('getUser')->willReturn($user);
        $user->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('isPasswordChanged')->willReturn(false);
        $router->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('generate')->willReturn('/url');
        $session->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('getFlashBag')->willReturn($flash);
        $trans->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('trans')->willReturn('translated-text');
        $adminRouteHelper->method('isAdminRoute')->willReturn($shouldPerformCheck);
        $requestStack = new RequestStack();
        $request = new Request();
        $request->setSession($session);
        $requestStack->push($request);

        $listener = new PasswordCheckListener($auth, $storage, $router, $requestStack, $trans, $adminRouteHelper);
        $listener->onKernelRequest($event);
    }

    public function requestDataProvider()
    {
        return [
            ['/en/random', false, 0],
            ['/en/admin/', true, 2],
            ['/en/admin/preview/', true, 2],
        ];
    }
}
