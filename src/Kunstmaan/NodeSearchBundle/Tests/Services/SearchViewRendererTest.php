<?php

namespace Kunstmaan\NodeSearchBundle\Tests\Services;

use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\NodeSearchBundle\Services\SearchViewRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class SearchViewRendererTest extends TestCase
{
    /**
     * @dataProvider htmlDataProvider
     */
    public function testRemoveHtml($input, $exptectedOutput)
    {
        $searchViewRenderer = new SearchViewRenderer(
            $this->createMock(Environment::class),
            $this->createMock(IndexablePagePartsService::class),
            new RequestStack(),
            new ServiceLocator([])
        );

        self::assertEquals($exptectedOutput, $searchViewRenderer->removeHtml($input));
    }

    public function htmlDataProvider(): \Iterator
    {
        yield ['le élève est ûn garçön', 'le élève est ûn garçön'];
        yield ['Hello world!', 'Hello world!'];
        yield ['<html><body><p>Hello world!</p></body></html>', 'Hello world!'];
        yield ['<div><b>le élève est ûn garçön</b></div>', 'le élève est ûn garçön'];
    }
}
