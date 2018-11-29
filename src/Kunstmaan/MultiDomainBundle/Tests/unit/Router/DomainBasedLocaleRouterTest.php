<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Router;

use Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DomainBasedLocaleRouterTest extends PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $request = $this->getRequest();
        $container = $this->getContainer($request);
        $object = new DomainBasedLocaleRouter($container);
        $url = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'en_GB'), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertEquals('http://multilangdomain.tld/en/some-uri', $url);

        $url = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'en_GB'), UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertEquals('/en/some-uri', $url);
    }

    public function testGenerateWithOtherSite()
    {
        $request = $this->getRequest();
        $request->setLocale('nl_BE');
        $container = $this->getContainer($request);
        $object = new DomainBasedLocaleRouter($container);
        $url = $object->generate('_slug', array('url' => 'some-uri', 'otherSite' => 'https://cia.gov'), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertEquals('http://multilangdomain.tld/nl/some-uri', $url);

        $url = $object->generate('_slug', array('url' => 'some-uri'), UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertEquals('/nl/some-uri', $url);
    }

    public function testGenerateWithLocaleBasedOnCurrentRequest()
    {
        $request = $this->getRequest();
        $request->setLocale('nl_BE');
        $container = $this->getContainer($request);
        $object = new DomainBasedLocaleRouter($container);
        $url = $object->generate('_slug', array('url' => 'some-uri'), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertEquals('http://multilangdomain.tld/nl/some-uri', $url);

        $url = $object->generate('_slug', array('url' => 'some-uri'), UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertEquals('/nl/some-uri', $url);
    }

    public function testMatchWithNodeTranslation()
    {
        $request = $this->getRequest();
        $nodeTranslation = new NodeTranslation();
        $container = $this->getContainer($request, $nodeTranslation);
        $object = new DomainBasedLocaleRouter($container);
        $result = $object->match('/en/some-uri');
        $this->assertEquals('some-uri', $result['url']);
        $this->assertEquals('en_GB', $result['_locale']);
        $this->assertEquals($nodeTranslation, $result['_nodeTranslation']);
    }

    public function testMatchWithoutNodeTranslation()
    {
        $this->expectException(ResourceNotFoundException::class);
        $request = $this->getRequest();
        $container = $this->getContainer($request);
        $object = new DomainBasedLocaleRouter($container);
        $object->match('/en/some-uri');
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddMultiLangSlugRoute()
    {
        $domainConfiguration = $this->createMock('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface');
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

        $node = $this->createMock('Kunstmaan\NodeBundle\Entity\Node');
        $domainConfiguration->method('getRootNode')
            ->willReturn($node);

        $domainConfiguration->method('getBackendLocales')
            ->willReturn(array('nl_BE', 'en_GB'));

        $request = $this->getRequest('http://singlelangdomain.tld/');

        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $serviceMap = array(
            array('request_stack', 1, $this->getRequestStack($request)),
            array('kunstmaan_admin.domain_configuration', 1, $domainConfiguration),
            array('doctrine.orm.entity_manager', 1, $this->getEntityManager(new NodeTranslation())),
        );

        $container
            ->method('get')
            ->will($this->returnValueMap($serviceMap));
        /** @var Container $container */
        $object = new DomainBasedLocaleRouter($container);

        $mirror = new ReflectionClass(DomainBasedLocaleRouter::class);
        $property = $mirror->getProperty('otherSite');
        $property->setAccessible(true);
        $property->setValue($object, ['host' => 'https://cia.gov']);
        $collection = $object->getRouteCollection();
        $array = $collection->getIterator()->getArrayCopy();
        $this->assertArrayHasKey('_slug', $array);
        $this->assertArrayHasKey('_slug_preview', $array);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetRouteCollection()
    {
        $domainConfiguration = $this->createMock('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface');
        $domainConfiguration->method('getHost')
            ->willReturn('override-domain.tld');

        $domainConfiguration->method('isMultiDomainHost')
            ->willReturn(false);

        $domainConfiguration->method('isMultiLanguage')
            ->willReturn(false);

        $domainConfiguration->method('getDefaultLocale')
            ->willReturn('nl_BE');

        $domainConfiguration->method('getFrontendLocales')
            ->willReturn(array('nl', 'en'));

        $node = $this->createMock('Kunstmaan\NodeBundle\Entity\Node');
        $domainConfiguration->method('getRootNode')
            ->willReturn($node);

        $domainConfiguration->method('getBackendLocales')
            ->willReturn(array('nl_BE', 'en_GB'));

        $request = $this->getRequest('http://singlelangdomain.tld/');

        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $serviceMap = array(
            array('request_stack', 1, $this->getRequestStack($request)),
            array('kunstmaan_admin.domain_configuration', 1, $domainConfiguration),
            array('doctrine.orm.entity_manager', 1, $this->getEntityManager(new NodeTranslation())),
        );

        $container
            ->method('get')
            ->will($this->returnValueMap($serviceMap));
        /** @var Container $container */
        $object = new DomainBasedLocaleRouter($container);
        $mirror = new ReflectionClass(DomainBasedLocaleRouter::class);
        $property = $mirror->getProperty('otherSite');
        $property->setAccessible(true);
        $property->setValue($object, ['host' => 'https://cia.gov']);
        $collection = $object->getRouteCollection();
        $array = $collection->getIterator()->getArrayCopy();
        $this->assertArrayHasKey('_slug', $array);
        $this->assertArrayHasKey('_slug_preview', $array);
    }

    /**
     * @param $request
     * @param null $nodeTranslation
     *
     * @return Container
     */
    private function getContainer($request, $nodeTranslation = null)
    {
        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $serviceMap = array(
            array('request_stack', 1, $this->getRequestStack($request)),
            array('kunstmaan_admin.domain_configuration', 1, $this->getDomainConfiguration()),
            array('doctrine.orm.entity_manager', 1, $this->getEntityManager($nodeTranslation)),
        );

        $container
            ->method('get')
            ->will($this->returnValueMap($serviceMap));

        /* @var Container $container */
        return $container;
    }

    private function getRequestStack($request)
    {
        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->any())->method('getMasterRequest')->willReturn($request);

        return $requestStack;
    }

    private function getDomainConfiguration()
    {
        $domainConfiguration = $this->createMock('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface');
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

        $node = $this->createMock('Kunstmaan\NodeBundle\Entity\Node');
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
        $em = $this->createMock('Doctrine\ORM\EntityManagerInterface');
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
