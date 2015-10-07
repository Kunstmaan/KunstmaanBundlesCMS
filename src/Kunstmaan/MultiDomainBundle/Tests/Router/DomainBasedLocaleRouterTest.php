<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Router;

use Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Symfony\Component\HttpFoundation\Request;

class DomainBasedLocaleRouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::generate
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::getRequestLocale
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::isMultiDomainHost
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::getReverseLocaleMap
     */
    public function testGenerate()
    {
        $request   = $this->getRequest();
        $container = $this->getContainer($request);
        $object    = new DomainBasedLocaleRouter($container);
        $url       = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'en_GB'), true);
        $this->assertEquals('http://multilangdomain.tld/en/some-uri', $url);

        $url = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'en_GB'), false);
        $this->assertEquals('/en/some-uri', $url);
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::generate
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::getRequestLocale
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::isMultiDomainHost
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::getReverseLocaleMap
     */
    public function testGenerateWithLocaleBasedOnCurrentRequest()
    {
        $request   = $this->getRequest();
        $request->setLocale('nl_BE');
        $container = $this->getContainer($request);
        $object    = new DomainBasedLocaleRouter($container);
        $url       = $object->generate('_slug', array('url' => 'some-uri'), true);
        $this->assertEquals('http://multilangdomain.tld/nl/some-uri', $url);

        $url = $object->generate('_slug', array('url' => 'some-uri'), false);
        $this->assertEquals('/nl/some-uri', $url);
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::match
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::getNodeTranslation
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::getLocaleMap
     */
    public function testMatchWithNodeTranslation()
    {
        $request   = $this->getRequest();
        $nodeTranslation = new NodeTranslation();
        $container = $this->getContainer($request, $nodeTranslation);
        $object    = new DomainBasedLocaleRouter($container);
        $result    = $object->match('/en/some-uri');
        $this->assertEquals('some-uri', $result['url']);
        $this->assertEquals('en_GB', $result['_locale']);
        $this->assertEquals($nodeTranslation, $result['_nodeTranslation']);
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter::match
     * @expectedException Symfony\Component\Routing\Exception\ResourceNotFoundException
     */
    public function testMatchWithoutNodeTranslation()
    {
        $request   = $this->getRequest();
        $container = $this->getContainer($request);
        $object    = new DomainBasedLocaleRouter($container);
        $object->match('/en/some-uri');
    }

    private function getContainer($request, $nodeTranslation = null)
    {
        $container    = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $serviceMap = array(
            array('request_stack', 1, $this->getRequestStack($request)),
            array('kunstmaan_admin.domain_configuration', 1, $this->getDomainConfiguration()),
            array('doctrine.orm.entity_manager', 1, $this->getEntityManager($nodeTranslation)),
        );

        $container
            ->method('get')
            ->will($this->returnValueMap($serviceMap));

        return $container;
    }

    private function getRequestStack($request)
    {
        $requestStack = $this->getMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->any())->method('getMasterRequest')->willReturn($request);

        return $requestStack;
    }

    private function getDomainConfiguration()
    {
        $domainConfiguration = $this->getMock('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface');
        $domainConfiguration->method('getHost')
            ->willReturn('override-domain.tld');

        $domainConfiguration->method('isMultiDomainHost')
            ->willReturn(true);

        $domainConfiguration->method('isMultiLanguage')
            ->willReturn(true);

        $domainConfiguration->method('getDefaultLocale')
            ->willReturn('nl_BE');

        $domainConfiguration->method('getFrontendLocales')
            ->willReturn(array('nl', 'en'));

        $node = $this->getMock('Kunstmaan\NodeBundle\Entity\Node');
        $domainConfiguration->method('getRootNode')
            ->willReturn($node);

        $domainConfiguration->method('getBackendLocales')
            ->willReturn(array('nl_BE', 'en_GB'));

        return $domainConfiguration;
    }

    private function getRequest($url = 'http://multilangdomain.tld/')
    {
        $request = Request::create($url);

        return $request;
    }

    private function getEntityManager($nodeTranslation = null)
    {
        $em = $this->getMock('Doctrine\ORM\EntityManagerInterface');
        $em
            ->method('getRepository')
            ->with($this->equalTo('KunstmaanNodeBundle:NodeTranslation'))
            ->willReturn($this->getNodeTranslationRepository($nodeTranslation));

        return $em;
    }

    private function getNodeTranslationRepository($nodeTranslation = null)
    {
        $repository = $this->getMockBuilder('Kunstmaan\NodeBundle\Repository\NodeTranslationRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('getNodeTranslationForUrl')
            ->willReturn($nodeTranslation);

        return $repository;
    }
}
