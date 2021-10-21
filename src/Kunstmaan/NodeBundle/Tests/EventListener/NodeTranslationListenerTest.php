<?php

namespace Kunstmaan\NodeBundle\Tests\EventListener;

use Kunstmaan\AdminBundle\Helper\DomainConfiguration;
use Kunstmaan\NodeBundle\EventListener\NodeTranslationListener;
use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use Kunstmaan\UtilitiesBundle\Helper\Slugifier;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class NodeTranslationListenerTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testDeprecatedConstructor()
    {
        $this->expectDeprecation('Passing a service instance of "Symfony\Component\HttpFoundation\Session\SessionInterface" for the first argument in "Kunstmaan\NodeBundle\EventListener\NodeTranslationListener::__construct" is deprecated since KunstmaanNodeBundle 5.10 and an instance of "Symfony\Component\HttpFoundation\RequestStack" will be required in KunstmaanNodeBundle 6.0.');
        $this->expectDeprecation('The fourth argument of "Kunstmaan\NodeBundle\EventListener\NodeTranslationListener::__construct" is deprecated since KunstmaanNodeBundle 5.10 and will be removed in KunstmaanNodeBundle 6.0. Check the constructor arguments and inject the required services instead.');

        $requestStack = new RequestStack();

        new NodeTranslationListener(new Session(), new NullLogger(), new Slugifier(), $requestStack, new DomainConfiguration($requestStack, true, 'en', 'en|nl'), new PagesConfiguration([]));
    }

    public function testNewConstructor()
    {
        $this->expectNotToPerformAssertions();

        $requestStack = new RequestStack();

        new NodeTranslationListener($requestStack, new NullLogger(), new Slugifier(), new DomainConfiguration($requestStack, true, 'en', 'en|nl'), new PagesConfiguration([]));
    }

    public function testInvalidSessionInConstructor()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The first argument of "Kunstmaan\NodeBundle\EventListener\NodeTranslationListener::__construct" should be instance of "Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface", "Symfony\Component\HttpFoundation\Session\SessionInterface" or "Symfony\Component\HttpFoundation\RequestStack"');

        $requestStack = new RequestStack();

        new NodeTranslationListener(new \stdClass(), new NullLogger(), new Slugifier(), new DomainConfiguration($requestStack, true, 'en', 'en|nl'), new PagesConfiguration([]));
    }

    public function testInvalidDomainConfigurationConstructor()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "$domainConfiguration" argument of "Kunstmaan\NodeBundle\EventListener\NodeTranslationListener::__construct" should be an instance of "Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface"');

        $requestStack = new RequestStack();

        new NodeTranslationListener($requestStack, new NullLogger(), new Slugifier(), new \stdClass(), new PagesConfiguration([]));
    }

    public function testInvalidPagesConfigurationConstructor()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('The "$pagesConfiguration" argument of "Kunstmaan\NodeBundle\EventListener\NodeTranslationListener::__construct" should be an instance of "Kunstmaan\NodeBundle\Helper\PagesConfiguration"');

        $requestStack = new RequestStack();

        new NodeTranslationListener($requestStack, new NullLogger(), new Slugifier(), new DomainConfiguration($requestStack, true, 'en', 'en|nl'), new \stdClass());
    }
}
