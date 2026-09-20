<?php

namespace Kunstmaan\AdminBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\EventListener\SessionSecurityListener;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class SessionSecurityListenerTest extends TestCase
{
    protected function tearDown(): void
    {
        Request::setTrustedProxies([], Request::HEADER_X_FORWARDED_FOR);
    }

    public function testResponseStoresClientIpAndUserAgentInSession(): void
    {
        $session = $this->createSession();
        $request = $this->createRequest($session, '10.0.0.1', 'Browser/1.0');

        $this->createListener()->onKernelResponse($this->createResponseEvent($request));

        $this->assertSame('10.0.0.1', $session->get('kuma_ip'));
        $this->assertSame('Browser/1.0', $session->get('kuma_ua'));
    }

    public function testResponseDoesNotOverwriteExistingSessionValues(): void
    {
        $session = $this->createSession(['kuma_ip' => '10.0.0.1', 'kuma_ua' => 'Browser/1.0']);
        $request = $this->createRequest($session, '10.0.0.2', 'Browser/2.0');

        $this->createListener()->onKernelResponse($this->createResponseEvent($request));

        $this->assertSame('10.0.0.1', $session->get('kuma_ip'));
        $this->assertSame('Browser/1.0', $session->get('kuma_ua'));
    }

    public function testRequestFromSameClientKeepsSession(): void
    {
        $session = $this->createSession(['kuma_ip' => '10.0.0.1', 'kuma_ua' => 'Browser/1.0']);
        $request = $this->createRequest($session, '10.0.0.1', 'Browser/1.0');

        $this->createListener()->onKernelRequest($this->createRequestEvent($request));

        $this->assertSame('10.0.0.1', $session->get('kuma_ip'));
    }

    public function testRequestFromOtherIpInvalidatesSession(): void
    {
        $session = $this->createSession(['kuma_ip' => '10.0.0.1', 'kuma_ua' => 'Browser/1.0', 'user_data' => 'secret']);
        $request = $this->createRequest($session, '10.0.0.2', 'Browser/1.0');

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('error')->with($this->stringContains("Session ip '10.0.0.1' does not match with request ip '10.0.0.2'"));

        $this->createListener($logger)->onKernelRequest($this->createRequestEvent($request));

        $this->assertFalse($session->has('user_data'));
        $this->assertSame('10.0.0.2', $session->get('kuma_ip'));
    }

    public function testRequestFromOtherUserAgentInvalidatesSession(): void
    {
        $session = $this->createSession(['kuma_ip' => '10.0.0.1', 'kuma_ua' => 'Browser/1.0', 'user_data' => 'secret']);
        $request = $this->createRequest($session, '10.0.0.1', 'Browser/2.0');

        $this->createListener()->onKernelRequest($this->createRequestEvent($request));

        $this->assertFalse($session->has('user_data'));
        $this->assertSame('Browser/2.0', $session->get('kuma_ua'));
    }

    public function testForwardedForHeaderIsIgnoredWithoutTrustedProxies(): void
    {
        $session = $this->createSession(['kuma_ip' => '10.0.0.1', 'kuma_ua' => 'Browser/1.0', 'user_data' => 'secret']);
        $request = $this->createRequest($session, '10.0.0.2', 'Browser/1.0', '10.0.0.1');

        $this->createListener()->onKernelRequest($this->createRequestEvent($request));

        $this->assertFalse($session->has('user_data'));
        $this->assertSame('10.0.0.2', $session->get('kuma_ip'));
    }

    public function testForwardedForHeaderIsUsedFromTrustedProxy(): void
    {
        Request::setTrustedProxies(['192.168.0.1'], Request::HEADER_X_FORWARDED_FOR);

        $session = $this->createSession(['kuma_ip' => '10.0.0.1', 'kuma_ua' => 'Browser/1.0', 'user_data' => 'secret']);
        $request = $this->createRequest($session, '192.168.0.1', 'Browser/1.0', '10.0.0.1');

        $this->createListener()->onKernelRequest($this->createRequestEvent($request));

        $this->assertTrue($session->has('user_data'));
    }

    public function testDisabledChecksAreSkipped(): void
    {
        $session = $this->createSession(['kuma_ip' => '10.0.0.1', 'kuma_ua' => 'Browser/1.0', 'user_data' => 'secret']);
        $request = $this->createRequest($session, '10.0.0.2', 'Browser/2.0');

        $this->createListener(null, false, false)->onKernelRequest($this->createRequestEvent($request));

        $this->assertTrue($session->has('user_data'));
    }

    public function testRequestWithoutSessionIsIgnored(): void
    {
        $listener = $this->createListener();
        $request = Request::create('/');

        $listener->onKernelRequest($this->createRequestEvent($request));
        $listener->onKernelResponse($this->createResponseEvent($request));

        $this->assertFalse($request->hasSession());
    }

    public function testSubRequestIsIgnored(): void
    {
        $session = $this->createSession(['kuma_ip' => '10.0.0.1', 'kuma_ua' => 'Browser/1.0', 'user_data' => 'secret']);
        $request = $this->createRequest($session, '10.0.0.2', 'Browser/2.0');

        $this->createListener()->onKernelRequest($this->createRequestEvent($request, HttpKernelInterface::SUB_REQUEST));

        $this->assertTrue($session->has('user_data'));
    }

    private function createListener(?LoggerInterface $logger = null, bool $ipCheck = true, bool $userAgentCheck = true): SessionSecurityListener
    {
        return new SessionSecurityListener($ipCheck, $userAgentCheck, $logger ?? $this->createMock(LoggerInterface::class));
    }

    private function createSession(array $data = []): Session
    {
        $session = new Session(new MockArraySessionStorage());
        $session->start();
        foreach ($data as $key => $value) {
            $session->set($key, $value);
        }

        return $session;
    }

    private function createRequest(Session $session, string $remoteAddr, string $userAgent, ?string $forwardedFor = null): Request
    {
        $server = ['REMOTE_ADDR' => $remoteAddr, 'HTTP_USER_AGENT' => $userAgent];
        if (null !== $forwardedFor) {
            $server['HTTP_X_FORWARDED_FOR'] = $forwardedFor;
        }

        $request = Request::create('/', 'GET', [], [], [], $server);
        $request->setSession($session);

        return $request;
    }

    private function createRequestEvent(Request $request, int $type = HttpKernelInterface::MAIN_REQUEST): RequestEvent
    {
        return new RequestEvent($this->createMock(KernelInterface::class), $request, $type);
    }

    private function createResponseEvent(Request $request): ResponseEvent
    {
        return new ResponseEvent($this->createMock(KernelInterface::class), $request, HttpKernelInterface::MAIN_REQUEST, new Response());
    }
}
