<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Router;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\HttpFoundation\RequestStack;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DomainBasedLocaleRouterTest extends TestCase
{
    public function testGenerate()
    {
        $request = $this->getRequest();
        $object = $this->getDomainBasedLocaleRouter($request);
        $url = $object->generate('_slug', ['url' => 'some-uri', '_locale' => 'en_GB'], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertSame('http://multilangdomain.tld/en/some-uri', $url);

        $url = $object->generate('_slug', ['url' => 'some-uri', '_locale' => 'en_GB'], UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertSame('/en/some-uri', $url);
    }

    public function testGenerateWithOtherSite()
    {
        $request = $this->getRequest();
        $request->setLocale('nl_BE');
        $object = $this->getDomainBasedLocaleRouter($request);
        $url = $object->generate('_slug', ['url' => 'some-uri', 'otherSite' => 'https://cia.gov'], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertSame('http://multilangdomain.tld/nl/some-uri', $url);

        $url = $object->generate('_slug', ['url' => 'some-uri'], UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertSame('/nl/some-uri', $url);
    }

    public function testGenerateWithLocaleBasedOnCurrentRequest()
    {
        $request = $this->getRequest();
        $request->setLocale('nl_BE');
        $object = $this->getDomainBasedLocaleRouter($request);
        $url = $object->generate('_slug', ['url' => 'some-uri'], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertSame('http://multilangdomain.tld/nl/some-uri', $url);

        $url = $object->generate('_slug', ['url' => 'some-uri'], UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertSame('/nl/some-uri', $url);
    }

    public function testMatchWithNodeTranslation()
    {
        $request = $this->getRequest();
        $nodeTranslation = new NodeTranslation();
        $object = $this->getDomainBasedLocaleRouter($request, $nodeTranslation);
        $result = $object->match('/en/some-uri');
        $this->assertSame('some-uri', $result['url']);
        $this->assertSame('en_GB', $result['_locale']);
        $this->assertEquals($nodeTranslation, $result['_nodeTranslation']);
    }

    public function testMatchWithoutNodeTranslation()
    {
        $this->expectException(ResourceNotFoundException::class);
        $request = $this->getRequest();
        $object = $this->getDomainBasedLocaleRouter($request);
        $object->match('/en/some-uri');
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddMultiLangSlugRoute()
    {
        $domainConfiguration = $this->createMock(DomainConfigurationInterface::class);
        $domainConfiguration->method('getHost')
            ->willReturn('override-domain.tld');

        $domainConfiguration->method('isMultiDomainHost')
            ->willReturn(true);

        $domainConfiguration->method('isMultiLanguage')
            ->willReturn(true);

        $domainConfiguration->method('getDefaultLocale')
            ->willReturn('nl_BE');

        $domainConfiguration->method('getFrontendLocales')
            ->willReturn(['nl', 'en']);

        $node = $this->createMock(Node::class);
        $domainConfiguration->method('getRootNode')
            ->willReturn($node);

        $domainConfiguration->method('getBackendLocales')
            ->willReturn(['nl_BE', 'en_GB']);

        $request = $this->getRequest('http://singlelangdomain.tld/');

        $object = new DomainBasedLocaleRouter($domainConfiguration, $this->getRequestStack($request), $this->getEntityManager(new NodeTranslation()), 'admin');

        $mirror = new \ReflectionClass(DomainBasedLocaleRouter::class);
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
        $domainConfiguration = $this->createMock(DomainConfigurationInterface::class);
        $domainConfiguration->method('getHost')
            ->willReturn('override-domain.tld');

        $domainConfiguration->method('isMultiDomainHost')
            ->willReturn(false);

        $domainConfiguration->method('isMultiLanguage')
            ->willReturn(false);

        $domainConfiguration->method('getDefaultLocale')
            ->willReturn('nl_BE');

        $domainConfiguration->method('getFrontendLocales')
            ->willReturn(['nl', 'en']);

        $node = $this->createMock(Node::class);
        $domainConfiguration->method('getRootNode')
            ->willReturn($node);

        $domainConfiguration->method('getBackendLocales')
            ->willReturn(['nl_BE', 'en_GB']);

        $request = $this->getRequest('http://singlelangdomain.tld/');

        /** @var Container $container */
        $object = new DomainBasedLocaleRouter($domainConfiguration, $this->getRequestStack($request), $this->getEntityManager(new NodeTranslation()), 'admin');
        $mirror = new \ReflectionClass(DomainBasedLocaleRouter::class);
        $property = $mirror->getProperty('otherSite');
        $property->setAccessible(true);
        $property->setValue($object, ['host' => 'https://cia.gov']);
        $collection = $object->getRouteCollection();
        $array = $collection->getIterator()->getArrayCopy();
        $this->assertArrayHasKey('_slug', $array);
        $this->assertArrayHasKey('_slug_preview', $array);
    }

    private function getRequestStack($request)
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getMainRequest')->willReturn($request);

        return $requestStack;
    }

    private function getDomainConfiguration()
    {
        $domainConfiguration = $this->createMock(DomainConfigurationInterface::class);
        $domainConfiguration->method('getHost')
            ->willReturn('override-domain.tld');

        $domainConfiguration->method('isMultiDomainHost')
            ->willReturn(true);

        $domainConfiguration->method('isMultiLanguage')
            ->willReturn(true);

        $domainConfiguration->method('getDefaultLocale')
            ->willReturn('nl_BE');

        $domainConfiguration->method('getFrontendLocales')
            ->willReturn(['nl', 'en']);

        $node = $this->createMock(Node::class);
        $domainConfiguration->method('getRootNode')
            ->willReturn($node);

        $domainConfiguration->method('getBackendLocales')
            ->willReturn(['nl_BE', 'en_GB']);

        return $domainConfiguration;
    }

    private function getRequest($url = 'http://multilangdomain.tld/')
    {
        return Request::create($url);
    }

    private function getEntityManager($nodeTranslation = null)
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em
            ->method('getRepository')
            ->with($this->equalTo(NodeTranslation::class))
            ->willReturn($this->getNodeTranslationRepository($nodeTranslation));

        return $em;
    }

    private function getNodeTranslationRepository($nodeTranslation = null)
    {
        $repository = $this->getMockBuilder(NodeTranslationRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->method('getNodeTranslationForUrl')
            ->willReturn($nodeTranslation);

        return $repository;
    }

    private function getDomainBasedLocaleRouter($request, $nodeTranslation = null)
    {
        return new DomainBasedLocaleRouter($this->getDomainConfiguration(), $this->getRequestStack($request), $this->getEntityManager($nodeTranslation), 'admin');
    }
}
