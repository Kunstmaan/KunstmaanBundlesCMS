<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\EventListener\SessionSecurityListener;
use PHPUnit_Framework_TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ServerBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class SessionSecurityListenerTest extends PHPUnit_Framework_TestCase
{
    public function testOnKernelRequest()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $request = $this->createMock(Request::class);
        $request->server = $this->createMock(ServerBag::class);
        $request->headers = $this->createMock(HeaderBag::class);

        $event = $this->createMock(GetResponseEvent::class);
        $session = $this->createMock(Session::class);

        $event->expects($this->any())->method('getRequestType')->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $event->expects($this->any())->method('getRequest')->willReturn($request);
        $request->expects($this->once())->method('hasSession')->willReturn(true);
        $request->expects($this->exactly(2))->method('getSession')->willReturn($session);
        $request->server->expects($this->any())->method('get')->will($this->onConsecutiveCalls('Session ip', 'kuma_ua'));
        $request->headers->expects($this->any())->method('get')->willReturn('kuma_ua');
        $session->expects($this->once())->method('isStarted')->willReturn(true);
        $session->expects($this->any())->method('has')->willReturn(true);

        $listener = new SessionSecurityListener(true, true, $logger);
        $listener->onKernelRequest($event);

        $event = $this->createMock(GetResponseEvent::class);
        $listener->onKernelRequest($event);
    }

    public function testOnKernelResponse()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $request = $this->createMock(Request::class);
        $request->server = $this->createMock(ServerBag::class);
        $request->headers = $this->createMock(HeaderBag::class);

        $event = $this->createMock(FilterResponseEvent::class);
        $session = $this->createMock(Session::class);

        $event->expects($this->any())->method('getRequestType')->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $event->expects($this->any())->method('getRequest')->willReturn($request);
        $request->expects($this->once())->method('hasSession')->willReturn(true);
        $request->expects($this->exactly(2))->method('getSession')->willReturn($session);
        $request->server->expects($this->any())->method('get')->will($this->onConsecutiveCalls('Session ip', 'kuma_ua'));
        $request->headers->expects($this->any())->method('get')->willReturn('kuma_ua');
        $session->expects($this->once())->method('isStarted')->willReturn(true);
        $session->expects($this->exactly(2))->method('has')->willReturn(false);

        $listener = new SessionSecurityListener(true, true, $logger);
        $listener->onKernelResponse($event);

        $event = $this->createMock(FilterResponseEvent::class);
        $listener->onKernelResponse($event);
    }

    public function testInvalidateSessionWithNoIpSet()
    {
        $logger = $this->createMock(LoggerInterface::class);
        $request = $this->createMock(Request::class);
        $request->server = $this->createMock(ServerBag::class);
        $request->headers = $this->createMock(HeaderBag::class);

        $event = $this->createMock(FilterResponseEvent::class);
        $session = $this->createMock(Session::class);

        $event->expects($this->any())->method('getRequestType')->willReturn(HttpKernelInterface::MASTER_REQUEST);
        $event->expects($this->any())->method('getRequest')->willReturn($request);
        $request->expects($this->once())->method('hasSession')->willReturn(true);
        $request->expects($this->exactly(2))->method('getSession')->willReturn($session);
        $request->expects($this->once())->method('getClientIp')->willReturn('95.154.243.5');
        $request->server->expects($this->any())->method('get')->willReturn('');
        $request->headers->expects($this->any())->method('get')->willReturn('kuma_ua');
        $session->expects($this->once())->method('isStarted')->willReturn(true);
        $session->expects($this->exactly(2))->method('has')->willReturn(false);

        $listener = new SessionSecurityListener(true, true, $logger);
        $listener->onKernelResponse($event);
    }
}
