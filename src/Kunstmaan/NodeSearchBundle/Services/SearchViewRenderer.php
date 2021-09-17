<?php

namespace Kunstmaan\NodeSearchBundle\Services;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\Helper\IndexablePagePartsService;
use Kunstmaan\NodeSearchBundle\Helper\SearchViewTemplateInterface;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class SearchViewRenderer
{
    /** @var Environment */
    private $twig;

    /** @var IndexablePagePartsService */
    private $indexablePagePartsService;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(Environment $twig, IndexablePagePartsService $indexablePagePartsService, RequestStack $requestStack)
    {
        $this->twig = $twig;
        $this->indexablePagePartsService = $indexablePagePartsService;
        $this->requestStack = $requestStack;
    }

    public function renderDefaultSearchView(NodeTranslation $nodeTranslation, HasPagePartsInterface $page, string $defaultView = '@KunstmaanNodeSearch/PagePart/view.html.twig')
    {
        $html = $this->twig->render($defaultView, [
            'locale' => $nodeTranslation->getLang(),
            'page' => $page,
            'pageparts' => $this->indexablePagePartsService->getIndexablePageParts($page),
            'indexMode' => true,
        ]);

        return $this->removeHtml($html);
    }

    public function renderCustomSearchView(NodeTranslation $nodeTranslation, SearchViewTemplateInterface $page, ContainerInterface $container = null)
    {
        $renderContext = new RenderContext([
            'locale' => $nodeTranslation->getLang(),
            'page' => $page,
            'indexMode' => true,
            'nodetranslation' => $nodeTranslation,
        ]);

        if ($page instanceof PageInterface && null !== $container) {
            $page->service($container, $this->requestStack->getCurrentRequest(), $renderContext);
        }

        $html = $this->twig->render($page->getSearchView(), $renderContext->getArrayCopy());

        return $this->removeHtml($html);
    }

    public function removeHtml(string $text): string
    {
        if (empty(trim($text))) {
            return '';
        }

        $crawler = new Crawler();
        $crawler->addHtmlContent($text);
        $crawler->filter('style, script')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });
        $text = $crawler->html();

        $result = strip_tags($text);
        $result = trim(html_entity_decode($result, ENT_QUOTES));

        return $result;
    }
}
