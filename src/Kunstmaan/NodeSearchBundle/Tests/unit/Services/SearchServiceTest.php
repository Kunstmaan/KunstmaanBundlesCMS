<?php

namespace Kunstmaan\NodeSearchBundle\Tests\unit\Services;

use Elastica\Query;
use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;
use Kunstmaan\NodeSearchBundle\Search\AbstractElasticaSearcher;
use Kunstmaan\NodeSearchBundle\Services\SearchService;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SearchServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group legacy
     * @expectedDeprecation Getting the node searcher "%s" from the container is deprecated in KunstmaanNodeSearchBundle 5.2 and will be removed in KunstmaanNodeSearchBundle 6.0. Tag your searcher service with the "kunstmaan_node_search.node_searcher" tag to add a searcher.
     */
    public function testLegacyContainerSearcher()
    {
        $entity = new AbstractSearchPage();
        $parameterBag = new ParameterBag();
        $parameterBag->add([
            '_entity' => $entity,
            'query' => '',
            'type' => 'html/text',
        ]);

        $request = $this->createMock(Request::class);
        $request->attributes = $parameterBag;
        $request->query = $parameterBag;

        $elasticSearcher = $this->createMock(AbstractElasticaSearcher::class);
        $elasticSearcher->method('setData')->willReturnSelf();
        $elasticSearcher->method('setContentType')->willReturnSelf();
        $elasticSearcher->method('getQuery')->willReturn(new Query());

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('kunstmaan_node_search.search.node')->willReturn($elasticSearcher);

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        $searcher = new SearchService($container, $requestStack, 10, []);

        $searcher->search();
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     */
    public function testUnknownSearcher()
    {
        $entity = $this->createMock(AbstractSearchPage::class);
        $entity->method('getSearcher')->willReturn('unknown_searcher');

        $parameterBag = new ParameterBag();
        $parameterBag->set('_entity', $entity);

        $request = $this->createMock(Request::class);
        $request->attributes = $parameterBag;
        $request->query = $parameterBag;

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with('unknown_searcher')->willThrowException(new ServiceNotFoundException('unknown_searcher'));

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        $searcher = new SearchService($container, $requestStack, 10, []);

        $searcher->search();
    }
}
