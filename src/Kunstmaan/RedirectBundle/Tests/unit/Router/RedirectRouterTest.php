<?php

namespace Kunstmaan\RedirectBundle\Tests\Router;

use Doctrine\Common\Persistence\ObjectRepository;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use Kunstmaan\RedirectBundle\Router\RedirectRouter;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\RequestContext;

/**
 * Class RedirectRouterTest
 */
class RedirectRouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RedirectRouter
     */
    protected $firstObject;

    /**
     * @var RedirectRouter
     */
    protected $secondObject;

    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var Redirect[]
     */
    protected $redirects;

    protected function setUp(): void
    {
        $firstDomainConfiguration = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface')
            ->disableOriginalConstructor()->getMock();
        $firstDomainConfiguration->expects($this->any())->method('getHost')->will($this->returnValue('sub.domain.com'));
        $firstRequestContext = $this->getMockBuilder(RequestContext::class)->disableOriginalConstructor()->getMock();
        $firstRequestContext->expects($this->any())->method('getHost')->willReturn('sub.domain.com');

        $secondDomainConfiguration = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface')
            ->disableOriginalConstructor()->getMock();
        $secondDomainConfiguration->expects($this->any())->method('getHost')->will($this->returnValue('other.domain.com'));
        $secondRequestContext = $this->getMockBuilder(RequestContext::class)->disableOriginalConstructor()->getMock();
        $secondRequestContext->expects($this->any())->method('getHost')->willReturn('other.domain.com');

        $this->repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->repository->expects($this->any())->method('findBy')->will($this->returnValue($this->getRedirects()));

        $this->firstObject = new RedirectRouter($this->repository, $firstDomainConfiguration);
        $this->secondObject = new RedirectRouter($this->repository, $secondDomainConfiguration);
        $this->firstObject->setContext($firstRequestContext);
        $this->secondObject->setContext($secondRequestContext);
    }

    public function testGetSetContext(): void
    {
        $context = new RequestContext();
        $this->firstObject->setContext($context);
        $this->assertEquals($context, $this->firstObject->getContext());
    }

    public function testGetRouteCollection(): void
    {
        $collection = $this->firstObject->getRouteCollection();
        $this->assertEquals(4, $collection->count());

        $collection = $this->secondObject->getRouteCollection();
        $this->assertEquals(7, $collection->count());
    }

    public function testGetRouteCollectionf(): void
    {
        $collection = $this->firstObject->getRouteCollection();
        $this->assertEquals(4, $collection->count());

        $collection = $this->secondObject->getRouteCollection();
        $this->assertEquals(7, $collection->count());
    }

    public function testGenerate(): void
    {
        $this->setExpectedException(RouteNotFoundException::class);
        $this->firstObject->generate('test');
    }

    public function testMatch(): void
    {
        $redirect = $this->firstObject->match('/test1');
        $this->assertEquals(
            [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => '/target1',
                'permanent' => false,
                '_route' => '_redirect_route_1',
            ],
            $redirect
        );

        $redirect = $this->firstObject->match('/test2');
        $this->assertEquals(
            [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => '/target2',
                'permanent' => true,
                '_route' => '_redirect_route_2',
            ],
            $redirect
        );

        $redirect = $this->firstObject->match('/test3');
        $this->assertEquals(
            [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => '/target3',
                'permanent' => true,
                '_route' => '_redirect_route_3',
            ],
            $redirect
        );

        $this->setExpectedException('Symfony\Component\Routing\Exception\ResourceNotFoundException');
        $this->firstObject->match('/testnotfound');

        $this->setExpectedException('Symfony\Component\Routing\Exception\ResourceNotFoundException');
        $this->firstObject->match('/test4');

        $redirect = $this->secondObject->match('/test4');
        $this->assertEquals(
            [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => '/target4',
                'permanent' => true,
                '_route' => '_redirect_route_4',
            ],
            $redirect
        );
    }

    public function testWildcardMatch(): void
    {
        $redirect = $this->firstObject->match('/test/nl');
        $this->assertEquals(
            [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => '/target5',
                'permanent' => false,
                '_route' => '_redirect_route_5',
                'wildcard' => 'nl',
            ],
            $redirect
        );

        $redirect = $this->secondObject->match('/test/nl');
        $this->assertEquals(
            [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => '/target5',
                'permanent' => false,
                '_route' => '_redirect_route_5',
                'wildcard' => 'nl',
            ],
            $redirect
        );

        $redirect = $this->secondObject->match('/test-fr/page');
        $this->assertEquals(
            [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => '/target7',
                'permanent' => false,
                '_route' => '_redirect_route_7',
                'wildcard' => 'page',
            ],
            $redirect
        );

        $redirect = $this->secondObject->match('/matchinganythingfallback');
        $this->assertEquals(
            [
                '_controller' => 'FrameworkBundle:Redirect:urlRedirect',
                'path' => '/target8',
                'permanent' => false,
                '_route' => '_redirect_route_8',
                'wildcard' => 'matchinganythingfallback',
            ],
            $redirect
        );
    }

    /**
     * @param int    $id
     * @param string $origin
     * @param string $target
     * @param bool   $permanent
     *
     * @return Redirect
     */
    private function getRedirect($id, $origin, $target, $permanent, $domain)
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

    /**
     * @return \Kunstmaan\RedirectBundle\Entity\Redirect[]
     */
    private function getRedirects()
    {
        if (!isset($this->redirects)) {
            $this->redirects = [];
            $this->redirects[] = $this->getRedirect(1, 'test1', '/target1', false, null);
            $this->redirects[] = $this->getRedirect(2, 'test2', '/target2', true, null);
            $this->redirects[] = $this->getRedirect(3, 'test3', '/target3', true, 'sub.domain.com');
            $this->redirects[] = $this->getRedirect(4, 'test4', '/target4', true, 'other.domain.com');
            $this->redirects[] = $this->getRedirect(5, 'test/*', '/target5', false, null);
            $this->redirects[] = $this->getRedirect(6, 'test/*', '/target6', false, 'other.domain.com');
            $this->redirects[] = $this->getRedirect(7, 'test-fr/*', '/target7', false, 'other.domain.com');
            $this->redirects[] = $this->getRedirect(8, '*', '/target8', false, 'other.domain.com');
        }

        return $this->redirects;
    }
}
