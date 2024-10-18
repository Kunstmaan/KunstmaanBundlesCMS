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
    public function testGetRouteCollection()
    {
        $router = new RedirectRouter($this->createMock(RedirectRepository::class), $this->createMock(DomainConfigurationInterface::class));
        $this->assertEquals(0, $router->getRouteCollection()->count());
    }

    public function testGenerateUnknownRoute()
    {
        $this->expectException(RouteNotFoundException::class);

        $router = new RedirectRouter($this->createMock(RedirectRepository::class), $this->createMock(DomainConfigurationInterface::class));
        $router->generate('test');
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

        $request = Request::create($requestUrl);

        $repository = $this->createMock(RedirectRepository::class);
        $repository
            ->method('findByRequestPathAndDomain')
            ->with($request->getPathInfo(), '')
            ->willReturn($redirect);

        $router = new RedirectRouter($repository, $firstDomainConfiguration);

        $context = new RequestContext();
        $router->setContext($context->fromRequest($request));
        $redirectResult = $router->match($request->getPathInfo());

        if (null !== $expectedRedirectUrl) {
            $this->assertSame($expectedRedirectUrl, $redirectResult['path']);
        }
    }

    public function urlProviderForImprovedRouter(): iterable
    {
        yield 'Standard redirect' => ['/test1', '/target1', $this->getRedirect(1, '/test1', '/target1')];
        yield 'Redirect with utf8 target path' => ['/test2', '/targét2', $this->getRedirect(2, '/test2', '/targét2')];
        yield 'Redirect with utf8 origin path' => ['/tést3', '/target3', $this->getRedirect(3, '/tést3', '/target3')];
        yield 'Catch-all wildcard origin redirect' => ['/wildcard/abc', '/target', $this->getRedirect(4, '/wildcard/*', '/target')];
        yield 'Wildcard origin and target redirect' => ['/wildcard/abc/def', '/target/abc/def', $this->getRedirect(5, '/wildcard/*', '/target/*')];
        yield 'Wildcard origin and target redirect with utf8' => ['/wildcard/tést', '/target/tést', $this->getRedirect(6, '/wildcard/*', '/target/*')];
        yield 'Wildcard origin to external target' => ['/wildcard/test', 'https://www.google.com', $this->getRedirect(7, '/wildcard/*', 'https://www.google.com')];
        yield 'Fixed origin to external target' => ['/fixed', 'https://www.google.com', $this->getRedirect(8, '/wildcard/*', 'https://www.google.com')];
        yield 'Unknown redirect' => ['/unknown-redirect', null, null];
        yield 'Wildcard origin to external wildcard target' => ['/wildcard/test', 'https://www.google.com/test', $this->getRedirect(9, '/wildcard/*', 'https://www.google.com/*')];
        yield 'Wildcard origin to wildcard root target' => ['/test', 'https://www.google.com/test', $this->getRedirect(10, '/*', 'https://www.google.com/*')];
        yield 'Wildcard root origin to wildcard root target redirect' => ['/test/abc/def', 'https://www.google.com/test/abc/def', $this->getRedirect(11, '/*', 'https://www.google.com/*')];
        yield 'Wildcard root origin to wildcard root target redirect with query params' => ['/test/abc/def?query=test', 'https://www.google.com/test/abc/def?query=test', $this->getRedirect(12, '/*', 'https://www.google.com/*')];
        yield 'Wildcard root origin to wildcard root target with root path should not redirect' => ['/', null, $this->getRedirect(13, '/*', 'https://www.google.com/*')];
        yield 'Redirect with query params' => ['/test?query=test', 'https://www.google.com/test?query=test', $this->getRedirect(14, '/test', 'https://www.google.com/test')];
    }

    private function getRedirect(int $id, string $origin, string $target, bool $permanent = false, ?string $domain = null): Redirect
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
