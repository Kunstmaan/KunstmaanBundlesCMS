<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\EventListener\AdminLocaleListener;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Translation\Translator;

class AdminLocaleListenerTest extends TestCase
{
    /**
     * @dataProvider requestDataProvider
     */
    public function testListener($uri, $shouldPerformCheck, $tokenStorageCallCount)
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => $uri]);
        $storage = $this->createMock(TokenStorageInterface::class);
        $trans = $this->createMock(Translator::class);
        $adminRouteHelper = $this->createMock(AdminRouteHelper::class);
        $kernel = $this->createMock(KernelInterface::class);
        $event = new RequestEvent($kernel, $request, HttpKernelInterface::MAIN_REQUEST);
        $user = $this->createMock(User::class);
        $token = new UsernamePasswordToken($user, 'main');

        $storage->expects($this->exactly($tokenStorageCallCount))->method('getToken')->willReturn($token);
        $user->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('getAdminLocale')->willReturn(null);
        $trans->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('setLocale');
        $adminRouteHelper->method('isAdminRoute')->willReturn($shouldPerformCheck);

        $listener = new AdminLocaleListener($storage, $trans, $adminRouteHelper, 'en');

        $events = AdminLocaleListener::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);

        $listener->onKernelRequest($event);
    }

    public function requestDataProvider()
    {
        return [
            ['/en/admin/', true, 1],
            ['/en/whatever/', false, 0],
            ['/en/admin/preview/', true, 1],
        ];
    }
}
