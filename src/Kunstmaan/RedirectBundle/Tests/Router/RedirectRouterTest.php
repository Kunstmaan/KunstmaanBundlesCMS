<?php

namespace Kunstmaan\RedirectBundle\Tests\Router;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use Kunstmaan\RedirectBundle\Repository\RedirectRepository;
use Kunstmaan\RedirectBundle\Router\RedirectRouter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;

class RedirectRouterTest extends TestCase
{
    private const MAIN_ROUTER = 'main';
    private const OTHER_DOMAIN_ROUTER = 'other';

    /** @var RedirectRouter[] */
    private $routers;

    protected function setUp(): void
    {
        $firstDomainConfiguration = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();
        $firstDomainConfiguration->method('getHost')->willReturn('sub.domain.com');

        $secondDomainConfiguration = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();
        $secondDomainConfiguration->method('getHost')->willReturn('other.domain.com');

        $repository = $this->createMock(RedirectRepository::class);
        $repository->method('findAll')->willReturn([
            $this->getRedirect(1, 'test1', '/target1', false, null),
            $this->getRedirect(2, 'test2', '/target2', true, null),
            $this->getRedirect(3, 'test3', '/target3', true, 'sub.domain.com'),
            $this->getRedirect(4, 'test4', '/target4', true, 'other.domain.com'),
            $this->getRedirect(5, 'test5', '/targét5', true, null),
            $this->getRedirect(6, 'tést6', '/target6', true, null),
            $this->getRedirect(7, '/wildcard/*', '/prefix/', true, null),
        ]);

        $this->routers[self::MAIN_ROUTER] = new RedirectRouter($repository, $firstDomainConfiguration);
        $this->routers[self::OTHER_DOMAIN_ROUTER] = new RedirectRouter($repository, $secondDomainConfiguration);

        $this->routers[self::MAIN_ROUTER]->enableImprovedRouter(true);
        $this->routers[self::OTHER_DOMAIN_ROUTER]->enableImprovedRouter(true);
    }

    public function testGetRouteCollection()
    {
        $this->assertEquals(6, $this->routers[self::MAIN_ROUTER]->getRouteCollection()->count());
        $this->assertEquals(6, $this->routers[self::OTHER_DOMAIN_ROUTER]->getRouteCollection()->count());
    }

    public function testGenerateUnknownRoute()
    {
        $this->expectException(RouteNotFoundException::class);
        $this->routers[self::MAIN_ROUTER]->generate('test');
    }

    /**
     * @group legacy
     * @dataProvider urlProvider
     */
    public function testRedirects(string $requestUrl, ?string $expectedRedirectUrl, string $routerType = self::MAIN_ROUTER)
    {
        if (null === $expectedRedirectUrl) {
            $this->expectException(ResourceNotFoundException::class);
        }

        $context = new RequestContext();
        $router = $this->routers[$routerType];
        $router->enableImprovedRouter(false);

        $router->setContext($context->fromRequest(Request::create($requestUrl)));
        $redirect = $router->match($requestUrl);

        if (null !== $expectedRedirectUrl) {
            $this->assertSame($expectedRedirectUrl, $redirect['path']);
            $this->assertSame('Symfony\Bundle\FrameworkBundle\Controller\RedirectController::urlRedirectAction', $redirect['_controller']);
        }
    }

    /**
     * @dataProvider urlProviderForImprovedRouter
     */
    public function testRedirectsForImprovedRouter(string $requestUrl, ?string $expectedRedirectUrl, ?Redirect $redirect)
    {
        if (null === $expectedRedirectUrl) {
            $this->expectException(ResourceNotFoundException::class);
        }

        $firstDomainConfiguration = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();
        $firstDomainConfiguration->method('getHost')->willReturn('');

        $repository = $this->createMock(RedirectRepository::class);
        $repository
            ->method('findByRequestPathAndDomain')
            ->with($requestUrl, '')
            ->willReturn($redirect);

        $router = new RedirectRouter($repository, $firstDomainConfiguration);
        $router->enableImprovedRouter(true);

        $context = new RequestContext();
        $router->setContext($context->fromRequest(Request::create($requestUrl)));
        $redirectResult = $router->match($requestUrl);

        if (null !== $expectedRedirectUrl) {
            $this->assertSame($expectedRedirectUrl, $redirectResult['path']);
        }
    }

    public function urlProvider(): array
    {
        return [
            ['/test1', '/target1'],
            ['/test2', '/target2'],
            ['/test3', '/target3'],
            ['/test4', '/target4', self::OTHER_DOMAIN_ROUTER],
            ['/test5', '/targét5'],
            ['/tést6', '/target6'],
            ['/wildcard/abc', '/prefix/abc'],
            ['/wildcard/abc/def', '/prefix/abc/def'],
            ['/unkown-redirect', null],
        ];
    }

    public function urlProviderForImprovedRouter(): iterable
    {
        yield 'Standard redirect' => ['/test1', '/target1', $this->getRedirect(1, '/test1', '/target1')];
        yield 'Redirect with utf8 target path' => ['/test2', '/targét2', $this->getRedirect(2, '/test2', '/targét2')];
        yield 'Redirect with utf8 origin path' => ['/tést3', '/target3', $this->getRedirect(3, '/tést3', '/target3')];
        yield 'Catch-all wildcard origin redirect' => ['/wildcard/abc', '/target', $this->getRedirect(4, '/wildcard/*', '/target')];
        yield 'Wildcard origin and target redirect' => ['/wildcard/abc/def', '/target/abc/def', $this->getRedirect(5, '/wildcard/*', '/target/*')];
        yield 'Wildcard origin and target redirect with utf8' => ['/wildcard/tést', '/target/tést', $this->getRedirect(6, '/wildcard/*', '/target/*')];
        yield 'Wildcard origin to external target' => ['/wildcard/test', 'https://www.google.com', $this->getRedirect(6, '/wildcard/*', 'https://www.google.com')];
        yield 'Fixed origin to external target' => ['/fixed', 'https://www.google.com', $this->getRedirect(6, '/wildcard/*', 'https://www.google.com')];
        yield 'Unknown redirect' => ['/unkown-redirect', null, null];
    }

    private function getRedirect(int $id, string $origin, string $target, bool $permanent = false, string $domain = null): Redirect
    {
        $redirect = new Redirect();
        $redirect
            ->setDomain($domain)
            ->setOrigin($origin)
            ->setTarget($target)
            ->setPermanent($permanent)
            ->setId($id);

        return $redirect;
    }
}
