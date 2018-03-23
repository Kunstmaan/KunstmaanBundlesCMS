<?php

namespace Tests\Kunstmaan\AdminBundle\EventListener;

use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\EventListener\AdminLocaleListener;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Translation\TranslatorInterface;

class AdminLocaleListenerTest extends PHPUnit_Framework_TestCase
{
    public function testListener()
    {
        $request = new Request([], [], [], [], [], ['REQUEST_URI' => '/en/admin/']);
        $storage = $this->createMock(TokenStorageInterface::class);
        $trans = $this->createMock(TranslatorInterface::class);
        $event = $this->createMock(GetResponseEvent::class);
        $token = $this->createMock(UsernamePasswordToken::class);
        $user = $this->createMock(User::class);

        $storage->expects($this->exactly(3))->method('getToken')->willReturn($token);
        $token->expects($this->exactly(3))->method('getProviderKey')->willReturn('main');
        $token->expects($this->once())->method('getUser')->willReturn($user);
        $event->expects($this->any())->method('getRequest')->willReturn($request);
        $user->expects($this->once())->method('getAdminLocale')->willReturn(null);
        $trans->expects($this->once())->method('setLocale')->willReturn(null);

        $listener = new AdminLocaleListener($storage, $trans, 'en');

        $events = AdminLocaleListener::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::REQUEST, $events);

        $listener->onKernelRequest($event);

        $request = $request->duplicate([], [], [], [], [], ['REQUEST_URI' => '/en/whatever/']);
        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->any())->method('getRequest')->willReturn($request);

        $listener->onKernelRequest($event);

        $request = $request->duplicate([], [], [], [], [], ['REQUEST_URI' => '/en/admin/preview/']);
        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->any())->method('getRequest')->willReturn($request);

        $listener->onKernelRequest($event);
    }
}