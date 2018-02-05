<?php

namespace Tests\Kunstmaan\MultiDomainBundle\Router;

use Kunstmaan\MultiDomainBundle\Router\DomainBasedLocaleRouter;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use PHPUnit_Framework_TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DomainBasedLocaleRouterTest extends PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $request   = $this->getRequest();
        $container = $this->getContainer($request);
        $object    = new DomainBasedLocaleRouter($container);
        $url       = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'en_GB'), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertEquals('http://multilangdomain.tld/en/some-uri', $url);

        $url = $object->generate('_slug', array('url' => 'some-uri', '_locale' => 'en_GB'), UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertEquals('/en/some-uri', $url);
    }

    public function testGenerateWithLocaleBasedOnCurrentRequest()
    {
        $request   = $this->getRequest();
        $request->setLocale('nl_BE');
        $container = $this->getContainer($request);
        $object    = new DomainBasedLocaleRouter($container);
        $url       = $object->generate('_slug', array('url' => 'some-uri'), UrlGeneratorInterface::ABSOLUTE_URL);
        $this->assertEquals('http://multilangdomain.tld/nl/some-uri', $url);

        $url = $object->generate('_slug', array('url' => 'some-uri'), UrlGeneratorInterface::ABSOLUTE_PATH);
        $this->assertEquals('/nl/some-uri', $url);
    }

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

    public function testMatchWithoutNodeTranslation()
    {
        $this->setExpectedException(ResourceNotFoundException::class);
        $request   = $this->getRequest();
        $container = $this->getContainer($request);
        $object    = new DomainBasedLocaleRouter($container);
        $object->match('/en/some-uri');
    }

    private function getContainer($request, $nodeTranslation = null)
    {
        $container    = $this->createMock('Symfony\Component\DependencyInjection\ContainerInterface');
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
