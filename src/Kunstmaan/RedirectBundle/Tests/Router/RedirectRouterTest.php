<?php

namespace Kunstmaan\RedirectBundle\Tests\Router;

use Doctrine\Common\Persistence\ObjectRepository;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use Kunstmaan\RedirectBundle\Router\RedirectRouter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\RequestContext;

class RedirectRouterTest extends TestCase
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
        $firstDomainConfiguration->expects($this->any())->method('getHost')->willReturn('sub.domain.com');

        $secondDomainConfiguration = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface')
            ->disableOriginalConstructor()->getMock();
        $secondDomainConfiguration->expects($this->any())->method('getHost')->willReturn('other.domain.com');

        $this->repository = $this->createMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->repository->expects($this->any())->method('findAll')->willReturn($this->getRedirects());

        $this->firstObject = new RedirectRouter($this->repository, $firstDomainConfiguration);
        $this->secondObject = new RedirectRouter($this->repository, $secondDomainConfiguration);
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
            $this->redirects[] = $this->getRedirect(5, 'test5', '/targét5', true, null);
            $this->redirects[] = $this->getRedirect(6, 'tést6', '/target6', true, null);
        }

        return $this->redirects;
    }

    public function testGetSetContext()
    {
        $context = new RequestContext();
        $this->firstObject->setContext($context);
        $this->assertEquals($context, $this->firstObject->getContext());
    }

    public function testGetRouteCollection()
    {
        $collection = $this->firstObject->getRouteCollection();
        $this->assertEquals(5, $collection->count());

        $collection = $this->secondObject->getRouteCollection();
        $this->assertEquals(5, $collection->count());
    }

    public function testGetRouteCollectionf()
    {
        $collection = $this->firstObject->getRouteCollection();
        $this->assertEquals(5, $collection->count());

        $collection = $this->secondObject->getRouteCollection();
        $this->assertEquals(5, $collection->count());
    }

    public function testGenerate()
    {
        $this->expectException(\Symfony\Component\Routing\Exception\RouteNotFoundException::class);
        $this->firstObject->generate('test');
    }

    public function testMatch()
    {
        $this->expectException(\Symfony\Component\Routing\Exception\ResourceNotFoundException::class);
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

        $this->firstObject->match('/testnotfound');

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
}
