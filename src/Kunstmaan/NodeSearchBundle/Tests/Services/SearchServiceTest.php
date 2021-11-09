<?php

namespace Kunstmaan\NodeSearchBundle\Tests\Services;

use Kunstmaan\NodeSearchBundle\Entity\AbstractSearchPage;
use Kunstmaan\NodeSearchBundle\Exception\SearcherServiceNotFoundException;
use Kunstmaan\NodeSearchBundle\Services\SearchService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SearchServiceTest extends TestCase
{
    public function testUnknownSearcher()
    {
        $this->expectException(SearcherServiceNotFoundException::class);

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
