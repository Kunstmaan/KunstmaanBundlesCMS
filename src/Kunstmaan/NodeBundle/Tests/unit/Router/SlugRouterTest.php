<?php

namespace Kunstmaan\NodeBundle\Tests\Router;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SlugRouterTest extends PHPUnit_Framework_TestCase
{
    public function testGenerateMultiLanguage()
    {
        $request = $this->getRequest();
        $container = $this->getContainer($request, true);
        $object = new SlugRouter($container);
        $url = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'en'), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertEquals('http://domain.tld/en/some-uri', $url);

        $url = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'en'), UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertEquals('/en/some-uri', $url);
    }

    public function testGenerateSingleLanguage()
    {
        $request = $this->getRequest();
        $container = $this->getContainer($request);
        $object = new SlugRouter($container);
        $url = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'nl'), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertEquals('http://domain.tld/some-uri', $url);

        $url = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'nl'), UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertEquals('/some-uri', $url);
    }

    public function testSetContext()
    {
        $context = $this->createMock('Symfony\Component\Routing\RequestContext');
        $container = $this->getContainer(null);
        $object = new SlugRouter($container);
        $object->setContext($context);
        $this->assertEquals($context, $object->getContext());
    }

    public function testMatchWithNodeTranslation()
    {
        $request = $this->getRequest();
        $nodeTranslation = new NodeTranslation();
        $container = $this->getContainer($request, true, $nodeTranslation);
        $object = new SlugRouter($container);
        $result = $object->match('/en/some-uri');
        $this->assertEquals('some-uri', $result['url']);
        $this->assertEquals('en', $result['_locale']);
        $this->assertEquals($nodeTranslation, $result['_nodeTranslation']);
    }

    public function testMatchWithoutNodeTranslation()
    {
        $this->setExpectedException(ResourceNotFoundException::class);
        $request = $this->getRequest();
        $container = $this->getContainer($request);
        $object = new SlugRouter($container);
        $object->match('/en/some-uri');
    }

    private function getContainer($request, $multiLanguage = false, $nodeTranslation = null)
    {
        $container = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
        $serviceMap = array(
            array('request_stack', 1, $this->getRequestStack($request)),
            array('kunstmaan_admin.domain_configuration', 1, $this->getDomainConfiguration($multiLanguage)),
            array('doctrine.orm.entity_manager', 1, $this->getEntityManager($nodeTranslation)),
        );

        $container
            ->method('get')
            ->will($this->returnValueMap($serviceMap));

        return $container;
    }

    private function getRequestStack($request)
    {
        $requestStack = $this->createMock('Symfony\Component\HttpFoundation\RequestStack');
        $requestStack->expects($this->any())->method('getMasterRequest')->willReturn($request);

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
            ->willReturn($multiLanguage ? array('nl', 'en') : array('nl'));

        $domainConfiguration->method('getBackendLocales')
            ->willReturn($multiLanguage ? array('nl', 'en') : array('nl'));

        $domainConfiguration->method('getRootNode')
            ->willReturn(null);

        return $domainConfiguration;
    }

    private function getRequest($url = 'http://domain.tld/')
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
