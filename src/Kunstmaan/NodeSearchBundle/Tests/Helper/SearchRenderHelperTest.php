<?php

namespace Kunstmaan\NodeBundle\Tests\Helper;

use Codeception\Test\Unit;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\NodeSearchBundle\Helper\SearchRenderHelper;
use Kunstmaan\NodeSearchBundle\Helper\SearchRenderHelperInterface;
use Kunstmaan\NodeSearchBundle\Helper\SearchViewTemplateInterface;
use Kunstmaan\PagePartBundle\Entity\HeaderPagePart;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class SearchRenderHelperTest.
 */
class SearchRenderHelperTest extends Unit
{
    const UTF8_TEXT = 'le élève est ûn garçön';

    /** @var SearchRenderHelper */
    private $searchRenderHelper;

    /** @var NodeTranslation */
    private $nodeTranslation;

    /** @var EngineInterface */
    private $engineInterface;

    /**
     * @throws \Exception
     */
    public function _before()
    {
        /** @var ContainerInterface $container */
        $container = $this->makeEmpty(ContainerInterface::class);

        /** @var RequestStack $requestStack */
        $requestStack = $this->makeEmpty(RequestStack::class, [
            'getCurrentRequest' => new Request(),
        ]);

        $headerPagePart = new HeaderPagePart();
        $headerPagePart
            ->setId(1)
            ->setTitle('Hello world')
            ->setNiv(1)
        ;

        /** @var IndexablePagePartsService $indexablePagePartsService */
        $indexablePagePartsService = $this->makeEmpty(IndexablePagePartsService::class);

        $this->searchRenderHelper = new SearchRenderHelper($container, $requestStack, $indexablePagePartsService);

        $this->nodeTranslation = $this->make(NodeTranslation::class, [
            'getLang' => 'nl',
        ]);

        $this->engineInterface = $this->makeEmpty(EngineInterface::class, [
            'render' => self::UTF8_TEXT,
        ]);

    }

    public function testHasInterface()
    {
        $this->assertInstanceOf(SearchRenderHelperInterface::class, $this->searchRenderHelper);
    }

    public function testRemoveHtml()
    {
        $text = sprintf('<div><b>%s</b></div>', self::UTF8_TEXT);
        $result = $this->searchRenderHelper->removeHtml($text);
        $this->assertEquals(self::UTF8_TEXT, $result);
    }

    /**
     * @throws \Exception
     */
    public function testRenderDefaultSearchView()
    {
        /** @var HasPagePartsInterface $hasPagePartsInterface */
        $hasPagePartsInterface =$this->makeEmpty(HasPagePartsInterface::class);
        $result = $this->searchRenderHelper->renderDefaultSearchView($this->nodeTranslation, $hasPagePartsInterface, $this->engineInterface);
        $this->assertEquals(self::UTF8_TEXT, $result);
    }

    /**
     * @throws \Exception
     */
    public function testRenderCustomSearchView()
    {
        /** @var SearchViewTemplateInterface $searchViewTemplateInterface */
        $searchViewTemplateInterface = $this->makeEmpty(SearchViewTemplateInterface::class);
        $result = $this->searchRenderHelper->renderCustomSearchView($this->nodeTranslation, $searchViewTemplateInterface, $this->engineInterface);
        $this->assertEquals(self::UTF8_TEXT, $result);
    }
}
