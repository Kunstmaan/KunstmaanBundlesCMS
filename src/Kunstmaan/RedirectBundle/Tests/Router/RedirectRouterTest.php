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

        $repository = $this->createMock(RedirectRepository::class);
        $repository
            ->method('findByRequestPathAndDomain')
            ->with($requestUrl, '')
            ->willReturn($redirect);

        $router = new RedirectRouter($repository, $firstDomainConfiguration);

        $context = new RequestContext();
        $router->setContext($context->fromRequest(Request::create($requestUrl)));
        $redirectResult = $router->match($requestUrl);

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
