<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Helper;

use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use Kunstmaan\MultiDomainBundle\Helper\HostOverrideCleanupHandler;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class HostOverrideCleanupHandlerTest extends PHPUnit_Framework_TestCase
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

    public function testLogoutWithoutOverride()
    {
        $request = Request::create('/');
        $response = new Response();
        $token    = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->object->logout($request, $response, $token);

        $this->assertFalse($request->hasSession());
    }

    public function testLogoutWithOverride()
    {
        $session = new Session(new MockArraySessionStorage());
        $session->set(DomainConfiguration::OVERRIDE_HOST, 'domain.tld');

        $request = Request::create('/');
        $request->setSession($session);
        $request->cookies->set($session->getName(), null);

        $response = new Response();
        $token    = $this->createMock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');

        $this->object->logout($request, $response, $token);

        $this->assertNull($session->get(DomainConfiguration::OVERRIDE_HOST));
    }
}
