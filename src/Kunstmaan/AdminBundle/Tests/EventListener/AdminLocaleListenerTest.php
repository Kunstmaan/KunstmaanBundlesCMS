<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\EventListener\AdminLocaleListener;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Translation\TranslatorInterface;

class AdminLocaleListenerTest extends TestCase
{
    /**
     * @dataProvider requestDataProvider
     */
    public function testListener($uri, $shouldPerformCheck, $tokenStorageCallCount)
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => $uri]);
        $storage = $this->createMock(TokenStorageInterface::class);
        $trans = $this->createMock(TranslatorInterface::class);
        $adminRouteHelper = $this->createMock(AdminRouteHelper::class);
        $event = $this->createMock(GetResponseEvent::class);
        $token = $this->createMock(UsernamePasswordToken::class);
        $user = $this->createMock(User::class);

        $storage->expects($this->exactly($tokenStorageCallCount))->method('getToken')->willReturn($token);
        $token->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('getProviderKey')->willReturn('main');
        $token->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('getUser')->willReturn($user);
        $event->expects($this->any())->method('getRequest')->willReturn($request);
        $user->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('getAdminLocale')->willReturn(null);
        $trans->expects($this->exactly($shouldPerformCheck ? 1 : 0))->method('setLocale')->willReturn(null);
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
