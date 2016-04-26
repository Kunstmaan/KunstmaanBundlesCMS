<?php

namespace Kunstmaan\MultiDomainBundle\Tests\EventListener;

use Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HostOverrideListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HostOverrideListener
     */
    protected $object;

    /**
     * @var
     */
    protected $session;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::isAdminRoute
     */
    public function testHostOverrideMessageIsSetForAdmin()
    {
        $flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->once())
            ->method('add')
            ->with('warning', 'multi_domain.host_override_active');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getFilterResponseEvent($this->getAdminRequest(), $this->getResponse());
        $object->onKernelResponse($event);
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::isAdminRoute
     */
    public function testHostOverrideMessageIsNotSetForAdminRedirectResponse()
    {
        $flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getFilterResponseEvent($this->getAdminRequest(), $this->getRedirectResponse());
        $object->onKernelResponse($event);
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::isAdminRoute
     */
    public function testHostOverrideMessageIsNotSetForSubRequest()
    {
        $flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getFilterResponseEvent($this->getAdminRequest(), $this->getResponse(), HttpKernelInterface::SUB_REQUEST);
        $object->onKernelResponse($event);
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::isAdminRoute
     */
    public function testHostOverrideMessageIsNotSetForXmlRequest()
    {
        $flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getFilterResponseEvent($this->getXmlHttpRequest(), $this->getResponse());
        $object->onKernelResponse($event);
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::isAdminRoute
     */
    public function testHostOverrideMessageIsNotSetForPreview()
    {
        $flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getFilterResponseEvent($this->getAdminPreviewRequest(), $this->getResponse());
        $object->onKernelResponse($event);
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::__construct
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::onKernelResponse
     * @covers Kunstmaan\MultiDomainBundle\EventListener\HostOverrideListener::isAdminRoute
     */
    public function testHostOverrideMessageIsNotSetForFrontend()
    {
        $flashBag = $this->getMock('Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface');
        $flashBag
            ->expects($this->never())
            ->method('add');

        $object = $this->getHostOverrideListener($flashBag);

        $event = $this->getFilterResponseEvent($this->getFrontendRequest(), $this->getResponse());
        $object->onKernelResponse($event);
    }

    private function getHostOverrideListener($flashBag)
    {
        $session = $this->getMockBuilder('Symfony\Component\HttpFoundation\Session\Session')
            ->disableOriginalConstructor()
            ->getMock();
        $session->method('getFlashBag')
            ->willReturn($flashBag);

        $domainConfiguration = $this->getMock('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface');
        $domainConfiguration->method('getHost')
            ->willReturn('override-domain.tld');

        $listener = new HostOverrideListener($session, $domainConfiguration);

        return $listener;
    }

    private function getFilterResponseEvent($request, $response, $requestType = HttpKernelInterface::MASTER_REQUEST)
    {
        $event = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $event->method('getRequestType')
            ->willReturn($requestType);

        $event->method('getResponse')
            ->willReturn($response);

        $event->method('getRequest')
            ->willReturn($request);

        return $event;
    }

    private function getResponse()
    {
        $response = $this->getMock('Symfony\Component\HttpFoundation\Response');

        return $response;
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
        $request = Request::create('http://domain.tld/nl/admin/some-admin-uri');

        return $request;
    }

    private function getAdminPreviewRequest()
    {
        $request = Request::create('http://domain.tld/nl/admin/preview/some-uri');

        return $request;
    }

    private function getFrontendRequest()
    {
        $request = Request::create('http://domain.tld/nl/some-uri');

        return $request;
    }
}
