<?php

namespace Kunstmaan\RedirectBundle\Tests\Router;

use Doctrine\Common\Persistence\ObjectRepository;
use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\RedirectBundle\Entity\Redirect;
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

    protected function setUp()
    {
        $firstDomainConfiguration = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();
        $firstDomainConfiguration->method('getHost')->willReturn('sub.domain.com');

        $secondDomainConfiguration = $this->getMockBuilder(DomainConfigurationInterface::class)->getMock();
        $secondDomainConfiguration->method('getHost')->willReturn('other.domain.com');

        $repository = $this->createMock(ObjectRepository::class);
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
     * @dataProvider urlProvider
     */
    public function testRedirects(string $requestUrl, ?string $expectedRedirectUrl, string $routerType = self::MAIN_ROUTER)
    {
        if (null === $expectedRedirectUrl) {
            $this->expectException(ResourceNotFoundException::class);
        }

        $context = new RequestContext();
        $router = $this->routers[$routerType];

        $router->setContext($context->fromRequest(Request::create($requestUrl)));
        $redirect = $router->match($requestUrl);

        if (null !== $expectedRedirectUrl) {
            $this->assertSame($expectedRedirectUrl, $redirect['path']);
            $this->assertSame('FrameworkBundle:Redirect:urlRedirect', $redirect['_controller']);
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

    private function getRedirect(int $id, string $origin, string $target, bool $permanent, ?string $domain): Redirect
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
