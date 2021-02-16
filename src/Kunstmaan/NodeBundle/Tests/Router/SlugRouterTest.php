<?php

namespace Kunstmaan\NodeBundle\Tests\Router;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class SlugRouterTest extends TestCase
{
    public function testGenerateMultiLanguage()
    {
        $object = new SlugRouter($this->getDomainConfiguration(true), $this->getRequestStack(1));
        $url = $object->generate('_slug', ['url' => 'some-uri', '_locale' => 'en'], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertEquals('http://domain.tld/en/some-uri', $url);

        $url = $object->generate('_slug', ['url' => 'some-uri', '_locale' => 'en'], UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertEquals('/en/some-uri', $url);
    }

    public function testGenerateSingleLanguage()
    {
        $object = new SlugRouter($this->getDomainConfiguration(), $this->getRequestStack(1));
        $url = $object->generate('_slug', ['url' => 'some-uri', '_locale' => 'nl'], UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertEquals('http://domain.tld/some-uri', $url);

        $url = $object->generate('_slug', ['url' => 'some-uri', '_locale' => 'nl'], UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertEquals('/some-uri', $url);
    }

    public function testSetContext()
    {
        $context = $this->createMock(RequestContext::class);
        $object = new SlugRouter($this->getDomainConfiguration(), $this->getRequestStack());
        $object->setContext($context);
        $this->assertEquals($context, $object->getContext());
    }

    public function testMatchWithNodeTranslation()
    {
        $nodeTranslation = new NodeTranslation();
        $object = new SlugRouter($this->getDomainConfiguration(true), $this->getRequestStack(1), $this->getEntityManager($nodeTranslation));
        $result = $object->match('/en/some-uri');

        $this->assertEquals('some-uri', $result['url']);
        $this->assertEquals('en', $result['_locale']);
        $this->assertEquals($nodeTranslation, $result['_nodeTranslation']);
    }

    public function testMatchWithoutNodeTranslation()
    {
        $this->expectException(ResourceNotFoundException::class);

        $object = new SlugRouter($this->getDomainConfiguration(), $this->getRequestStack(1), $this->getEntityManager());
        $object->match('/en/some-uri');
    }

    private function getRequestStack($callCount = 0)
    {
        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->expects($this->exactly($callCount))->method('getMasterRequest')->willReturn(Request::create('http://domain.tld/'));

        return $requestStack;
    }

    private function getDomainConfiguration($multiLanguage = false)
    {
        $domainConfiguration = $this->createMock('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface');
        $domainConfiguration->method('getHost')
            ->willReturn('domain.tld');

        $domainConfiguration->method('isMultiDomainHost')
            ->willReturn(false);

        $domainConfiguration->method('isMultiLanguage')
            ->willReturn($multiLanguage);

        $domainConfiguration->method('getDefaultLocale')
            ->willReturn('nl');

        $domainConfiguration->method('getFrontendLocales')
            ->willReturn($multiLanguage ? ['nl', 'en'] : ['nl']);

        $domainConfiguration->method('getBackendLocales')
            ->willReturn($multiLanguage ? ['nl', 'en'] : ['nl']);

        $domainConfiguration->method('getRootNode')
            ->willReturn(null);

        return $domainConfiguration;
    }

    private function getEntityManager($nodeTranslation = null)
    {
        $em = $this->createMock('Doctrine\ORM\EntityManagerInterface');
        $em
            ->method('getRepository')
            ->with($this->equalTo(NodeTranslation::class))
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
