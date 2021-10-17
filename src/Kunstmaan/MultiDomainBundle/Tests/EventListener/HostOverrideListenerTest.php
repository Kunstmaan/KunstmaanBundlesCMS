<?php

namespace Kunstmaan\MultiDomainBundle\Tests\EventListener;

use Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Translation\Translator;

class HostOverrideListenerTest extends TestCase
{
    /**
     * @var HostOverrideListener
     */
    protected $object;

    protected $session;

    /**
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     */
    public function testHostOverrideMessageIsSetForAdmin()
    {
        $flashBag = $this->createMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('warning', 'multi_domain.host_override_active');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getEvent($this->getAdminRequest(), $this->getResponse());
        $object->onKernelResponse($event);
    }

    /**
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     */
    public function testHostOverrideMessageIsNotSetForAdminRedirectResponse()
    {
        $flashBag = $this->createMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getEvent($this->getAdminRequest(), $this->getRedirectResponse());
        $object->onKernelResponse($event);
    }

    /**
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     */
    public function testHostOverrideMessageIsNotSetForSubRequest()
    {
        $flashBag = $this->createMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getEvent($this->getAdminRequest(), $this->getResponse(), HttpKernelInterface::SUB_REQUEST);
        $object->onKernelResponse($event);
    }

    /**
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     */
    public function testHostOverrideMessageIsNotSetForXmlRequest()
    {
        $flashBag = $this->createMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getEvent($this->getXmlHttpRequest(), $this->getResponse());
        $object->onKernelResponse($event);
    }

    /**
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     */
    public function testHostOverrideMessageIsNotSetForPreview()
    {
        $flashBag = $this->createMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getEvent($this->getAdminPreviewRequest(), $this->getResponse());
        $object->onKernelResponse($event);
    }

    /**
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers \Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     */
    public function testHostOverrideMessageIsNotSetForFrontend()
    {
        $flashBag = $this->createMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getEvent($this->getFrontendRequest(), $this->getResponse());
        $object->onKernelResponse($event);
    }

    private function getHostOverrideListener($flashBag)
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $session->method('getFlashBag')
            ->willReturn($flashBag);

        $domainConfiguration = $this->createMock('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface');
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

        $adminRouteHelper = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\AdminRouteHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $adminRouteHelper
            ->expects($this->any())
            ->method('isAdminRoute')
            ->willReturnMap($adminRouteReturnValueMap);

        $listener = new HostOverrideListener($session, $translator, $domainConfiguration, $adminRouteHelper);

        return $listener;
    }

    private function getEvent($request, $response, $requestType = HttpKernelInterface::MASTER_REQUEST)
    {
        $kernel = $this->createMock(KernelInterface::class);

        return new ResponseEvent($kernel, $request, $requestType, $response);
    }

    private function getResponse()
    {
        return $this->createMock('Symfony\Component\HttpFoundation\Response');
    }

    private function getRedirectResponse()
    {
        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\RedirectResponse')
            ->disableOriginalConstructor()
            ->getMock();

        return $response;
    }

    private function getXmlHttpRequest()
    {
        $request = Request::create('http://domain.tld/nl/admin/some-admin-uri');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        return $request;
    }

    private function getAdminRequest()
    {
        return Request::create('http://domain.tld/nl/admin/some-admin-uri');
    }

    private function getAdminPreviewRequest()
    {
        return Request::create('http://domain.tld/nl/admin/preview/some-uri');
    }

    private function getFrontendRequest()
    {
        return Request::create('http://domain.tld/nl/some-uri');
    }
}
