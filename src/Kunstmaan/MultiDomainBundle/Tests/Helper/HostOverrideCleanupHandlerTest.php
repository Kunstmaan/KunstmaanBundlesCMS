<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Helper;

use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Kunstmaan\MultiDomainBundle\Helper\HostOverrideCleanupHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HostOverrideCleanupHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HostOverrideCleanupHandler
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new HostOverrideCleanupHandler();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\Helper\HostOverrideCleanupHandler::logout
     */
    public function testLogoutWithoutOverride()
    {
        $request = Request::create('/');

        $headerBag = $this->getMock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $headerBag->expects($this->never())
            ->method('clearCookie')
            ->with($this->equalTo(DomainConfiguration::OVERRIDE_HOST));

        $response = $this->getMockResponse($headerBag);
        $token    = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->object->logout($request, $response, $token);
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\Helper\HostOverrideCleanupHandler::logout
     */
    public function testLogoutWithOverride()
    {
        $request = Request::create('/');
        $request->cookies->set(DomainConfiguration::OVERRIDE_HOST, 'domain.tld');

        $headerBag = $this->getMock('Symfony\Component\HttpFoundation\ResponseHeaderBag');
        $headerBag->expects($this->once())
            ->method('clearCookie')
            ->with($this->equalTo(DomainConfiguration::OVERRIDE_HOST));

        $response = $this->getMockResponse($headerBag);
        $token    = $this->getMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->object->logout($request, $response, $token);
    }

    private function getMockResponse($headerBag)
    {
        $response          = new Response();
        $response->headers = $headerBag;

        return $response;
    }
}
