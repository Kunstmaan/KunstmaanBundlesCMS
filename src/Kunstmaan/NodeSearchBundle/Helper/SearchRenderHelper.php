<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class SearchRenderHelper.
 *
 * @final
 */
class SearchRenderHelper implements SearchRenderHelperInterface
{
    /** @var IndexablePagePartsService */
    protected $indexablePagePartsService;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * SearchRenderHelper constructor.
     *
     * @param ContainerInterface        $container
     * @param RequestStack              $requestStack
     * @param IndexablePagePartsService $indexablePagePartsService
     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, IndexablePagePartsService $indexablePagePartsService)
    {
        $this->indexablePagePartsService = $indexablePagePartsService;
        $this->requestStack = $requestStack;
        $this->container = $container;
    }

    /**
     * Removes all HTML markup & decode HTML entities
     *
     * @param string $text
     *
     * @return string
     */
    public function removeHtml($text)
    {
        if (!trim($text)) {
            return '';
        }

        // Remove Styles and Scripts
        $crawler = new Crawler();
        $crawler->addHtmlContent($text);
        $crawler->filter('style, script')->each(function (Crawler $crawler) {
            foreach ($crawler as $node) {
                $node->parentNode->removeChild($node);
            }
        });
        $text = $crawler->html();

        // Remove HTML markup
        $result = strip_tags($text);

        // Decode HTML entities
        $result = trim(html_entity_decode($result, ENT_QUOTES));

        return $result;
    }

    /**
     * Render default search view (all indexable pageparts in the main context
     * of the page)
     *
     * @param NodeTranslation       $nodeTranslation
     * @param HasPagePartsInterface $page
     * @param EngineInterface       $renderer
     *
     * @return string
     */
    public function renderDefaultSearchView(NodeTranslation $nodeTranslation, HasPagePartsInterface $page, EngineInterface $renderer) {
        $pageparts = $this->indexablePagePartsService->getIndexablePageParts($page);
        $view      = 'KunstmaanNodeSearchBundle:PagePart:view.html.twig';
        $content   = $this->removeHtml(
            $renderer->render(
                $view,
                [
                    'locale' => $nodeTranslation->getLang(),
                    'page' => $page,
                    'pageparts' => $pageparts,
                    'indexMode' => true,
                ]
            )
        );

        return $content;
    }

    /**
     * Render a custom search view
     *
     * @param NodeTranslation             $nodeTranslation
     * @param SearchViewTemplateInterface $page
     * @param EngineInterface             $renderer
     *
     * @return string
     */
    public function renderCustomSearchView(NodeTranslation $nodeTranslation, SearchViewTemplateInterface $page, EngineInterface $renderer) {
        $view = $page->getSearchView();
        $renderContext = new RenderContext([
            'locale'          => $nodeTranslation->getLang(),
            'page'            => $page,
            'indexMode'       => true,
            'nodetranslation' => $nodeTranslation,
        ]);

        if ($page instanceof PageInterface) {
            $request = $this->requestStack->getCurrentRequest();
            $page->service($this->container, $request, $renderContext);
        }

        $content = $this->removeHtml(
            $renderer->render(
                $view,
                $renderContext->getArrayCopy()
            )
        );

        return $content;
    }
}
