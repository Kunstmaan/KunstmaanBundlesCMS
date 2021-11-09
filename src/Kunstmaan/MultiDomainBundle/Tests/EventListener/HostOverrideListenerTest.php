<?php

namespace Kunstmaan\MultiDomainBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\Translator;

class HostOverrideListenerTest extends TestCase
{
    use ExpectDeprecationTrait;

    public function testHostOverrideMessageIsSetForAdmin()
    {
        $listener = $this->getHostOverrideListener();

        $event = $this->getEvent($this->getAdminRequest(), new Response());
        $listener->onKernelResponse($event);

        $flashbag = $event->getRequest()->getSession()->getFlashBag();
        $this->assertContains('multi_domain.host_override_active', $flashbag->get('warning'));
    }

    public function testHostOverrideMessageIsNotSetForAdminRedirectResponse()
    {
        $listener = $this->getHostOverrideListener();

        $event = $this->getEvent($this->getAdminRequest(), new RedirectResponse('/redirect'));
        $listener->onKernelResponse($event);

        $flashbag = $event->getRequest()->getSession()->getFlashBag();
        $this->assertCount(0, $flashbag->all());
    }

    public function testHostOverrideMessageIsNotSetForSubRequest()
    {
        $listener = $this->getHostOverrideListener();

        $event = $this->getEvent($this->getAdminRequest(), new Response(), HttpKernelInterface::SUB_REQUEST);
        $listener->onKernelResponse($event);

        $flashbag = $event->getRequest()->getSession()->getFlashBag();
        $this->assertCount(0, $flashbag->all());
    }

    public function testHostOverrideMessageIsNotSetForXmlRequest()
    {
        $listener = $this->getHostOverrideListener();

        $request = Request::create('http://domain.tld/nl/admin/some-admin-uri');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $request->setSession($this->getSession());

        $event = $this->getEvent($request, new Response());
        $listener->onKernelResponse($event);

        $flashbag = $event->getRequest()->getSession()->getFlashBag();
        $this->assertCount(0, $flashbag->all());
    }

    public function testHostOverrideMessageIsNotSetForPreview()
    {
        $listener = $this->getHostOverrideListener();

        $request = Request::create('http://domain.tld/nl/admin/preview/some-uri');
        $request->setSession($this->getSession());

        $event = $this->getEvent($request, new Response());
        $listener->onKernelResponse($event);

        $flashbag = $event->getRequest()->getSession()->getFlashBag();
        $this->assertCount(0, $flashbag->all());
    }

    public function testHostOverrideMessageIsNotSetForFrontend()
    {
        $listener = $this->getHostOverrideListener();

        $request = Request::create('http://domain.tld/nl/some-uri');
        $request->setSession($this->getSession());

        $event = $this->getEvent($request, new Response());
        $listener->onKernelResponse($event);

        $flashbag = $event->getRequest()->getSession()->getFlashBag();
        $this->assertCount(0, $flashbag->all());
    }

    private function getHostOverrideListener()
    {
        $domainConfiguration = $this->createMock(DomainConfigurationInterface::class);
        $domainConfiguration->method('getHost')
            ->willReturn('override-domain.tld');
        $translator = $this->createMock(Translator::class);
        $translator->method('trans')
            ->willReturnArgument(0);

        $adminRouteReturnValueMap = [
            ['/nl/admin/preview/some-uri', false],
            ['/nl/some-uri', false],
            ['/nl/admin/some-admin-uri', true],
        ];

        $adminRouteHelper = $this->getMockBuilder(AdminRouteHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $adminRouteHelper
            ->expects($this->any())
            ->method('isAdminRoute')
            ->willReturnMap($adminRouteReturnValueMap);

        return new HostOverrideListener($translator, $domainConfiguration, $adminRouteHelper);
    }

    private function getEvent($request, $response, $requestType = HttpKernelInterface::MASTER_REQUEST)
    {
        $kernel = $this->createMock(KernelInterface::class);

        return class_exists(RequestEvent::class) ?
            new ResponseEvent($kernel, $request, $requestType, $response) :
            new FilterResponseEvent($kernel, $request, $requestType, $response)
        ;
    }

    private function getAdminRequest()
    {
        $request = Request::create('http://domain.tld/nl/admin/some-admin-uri');
        $request->setSession($this->getSession());

        return $request;
    }

    private function getSession(): Session
    {
        return new Session(new MockArraySessionStorage());
    }
}
