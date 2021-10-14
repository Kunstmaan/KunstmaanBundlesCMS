<?php

namespace Kunstmaan\MultiDomainBundle\Tests\EventSubscriber;

use Kunstmaan\MultiDomainBundle\EventSubscriber\LogoutHostOverrideCleanupEventSubscriber;
use Kunstmaan\MultiDomainBundle\Helper\DomainConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutHostOverrideCleanupEventSubscriberTest extends TestCase
{
    public function testLogoutWithoutOverride()
    {
        if (!class_exists(LogoutEvent::class)) {
            $this->markTestSkipped('Don\'t run test on symfony 4');
        }

        $request = Request::create('/');
        $token = $this->createMock(TokenInterface::class);

        $subscriber = new LogoutHostOverrideCleanupEventSubscriber();
        $subscriber->cleanupHostOverrideSession(new LogoutEvent($request, $token));

        $this->assertFalse($request->hasSession());
    }

    public function testLogoutWithOverride()
    {
        if (!class_exists(LogoutEvent::class)) {
            $this->markTestSkipped('Don\'t run test on symfony 4');
        }

        $session = new Session(new MockArraySessionStorage());
        $session->set(DomainConfiguration::OVERRIDE_HOST, 'domain.tld');

        $request = Request::create('/');
        $request->setSession($session);
        $request->cookies->set($session->getName(), null);

        $token = $this->createMock(TokenInterface::class);

        $subscriber = new LogoutHostOverrideCleanupEventSubscriber();
        $subscriber->cleanupHostOverrideSession(new LogoutEvent($request, $token));

        $this->assertNull($session->get(DomainConfiguration::OVERRIDE_HOST));
    }
}
