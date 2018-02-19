<?php

namespace Kunstmaan\NodeSearchBundle\Helper;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\PageInterface;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\Configuration\HasCustomSearchDataInterface;
use Kunstmaan\NodeSearchBundle\Event\IndexNodeEvent;
use Kunstmaan\NodeSearchBundle\Event\RenderPageEvent;
use Kunstmaan\PagePartBundle\Helper\HasPagePartsInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class PageHelper
 *
 * @package Kunstmaan\NodeSearchBundle\Helper
 */
class PageHelper
{
    /** @var EngineInterface */
    private $templating;

    /** @var RequestStack */
    private $requestStack;

    /** @var IndexablePagePartsService */
    private $indexablePagePartsService;

    /** @var RouterInterface */
    private $router;

    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * PageHelper constructor.
     *
     * @param EngineInterface           $templating
     * @param RequestStack              $requestStack
     * @param IndexablePagePartsService $indexablePagePartsService
     * @param RouterInterface           $router
     * @param LoggerInterface           $logger
     * @param EventDispatcherInterface  $eventDispatcher
     */
    public function __construct(
        EngineInterface $templating,
        RequestStack $requestStack,
        IndexablePagePartsService $indexablePagePartsService,
        RouterInterface $router,
        LoggerInterface $logger,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->templating = $templating;
        $this->requestStack = $requestStack;
        $this->indexablePagePartsService = $indexablePagePartsService;
        $this->router = $router;
        $this->logger = $logger;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Add page content to the index document
     *
     * @param NodeTranslation  $nodeTranslation
     * @param HasNodeInterface $page
     * @param array            $doc
     *
     * @return null
     */
    public function addPageContent(NodeTranslation $nodeTranslation, $page, &$doc)
    {
        $this->enterRequestScope($nodeTranslation->getLang());
        if ($this->logger) {
            $this->logger->debug(
                sprintf(
                    'Indexing page "%s" / lang : %s / type : %s / id : %d / node id : %d',
                    $page->getTitle(),
                    $nodeTranslation->getLang(),
                    \get_class($page),
                    $page->getId(),
                    $nodeTranslation->getNode()->getId()
                )
            );
        }

        $doc['content'] = '';

        if ($page instanceof SearchViewTemplateInterface) {
            $doc['content'] = $this->renderCustomSearchView($nodeTranslation, $page);

            return null;
        }

        if ($page instanceof HasPagePartsInterface) {
            $doc['content'] = $this->renderDefaultSearchView($nodeTranslation, $page);

            return null;
        }
    }

    /**
     * Render a custom search view
     *
     * @param NodeTranslation             $nodeTranslation
     * @param SearchViewTemplateInterface $page
     *
     * @return string
     */
    protected function renderCustomSearchView(NodeTranslation $nodeTranslation, SearchViewTemplateInterface $page)
    {
        $view = $page->getSearchView();
        $renderContext = new RenderContext(
            [
                'locale' => $nodeTranslation->getLang(),
                'page' => $page,
                'indexMode' => true,
                'nodetranslation' => $nodeTranslation,
            ]
        );

        if ($page instanceof PageInterface) {
            $request = $this->requestStack->getCurrentRequest();

            if (null !== $request) {
                $event = new RenderPageEvent($page, $renderContext, $request);
                $this->eventDispatcher->dispatch(RenderPageEvent::EVENT_RENDER_PAGE, $event);

                $renderContext = $event->getRenderContext();
            }
        }

        $content = $this->removeHtml(
            $this->templating->render(
                $view,
                $renderContext->getArrayCopy()
            )
        );

        return $content;
    }

    /**
     * Render default search view (all indexable pageparts in the main context
     * of the page)
     *
     * @param NodeTranslation       $nodeTranslation
     * @param HasPagePartsInterface $page
     *
     * @return string
     */
    protected function renderDefaultSearchView(NodeTranslation $nodeTranslation, HasPagePartsInterface $page)
    {
        $pageparts = $this->indexablePagePartsService->getIndexablePageParts($page);

        $content = $this->removeHtml(
            $this->templating->render(
                $this->getDefaultSearchViewTemplate(),
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
     * Add custom data to index document (you can override to add custom fields
     * to the search index)
     *
     * @param HasNodeInterface $page
     * @param array            $doc
     */
    public function addCustomData(HasNodeInterface $page, &$doc)
    {
        $event = new IndexNodeEvent($page, $doc);
        $this->eventDispatcher->dispatch(IndexNodeEvent::EVENT_INDEX_NODE, $event);

        $doc = $event->doc;

        if ($page instanceof HasCustomSearchDataInterface) {
            $doc += $page->getCustomSearchData($doc);
        }
    }

    /**
     * Removes all HTML markup & decode HTML entities
     *
     * @param $text
     *
     * @return string
     */
    protected function removeHtml($text)
    {
        if (!trim($text)) {
            return '';
        }

        // Remove Styles and Scripts
        $crawler = new Crawler($text);
        $crawler->filter('style, script')->each(
            function (Crawler $crawler) {
                foreach ($crawler as $node) {
                    $node->parentNode->removeChild($node);
                }
            }
        );

        $text = $crawler->html();
        // Remove HTML markup
        $result = strip_tags($text);
        // Decode HTML entities
        $result = trim(html_entity_decode($result, ENT_QUOTES));

        return $result;
    }

    /**
     * Enter request scope if it is not active yet...
     *
     * @param string $lang
     */
    protected function enterRequestScope($lang)
    {
        // If there already is a request, get the locale from it.
        if ($this->requestStack->getCurrentRequest()) {
            $locale = $this->requestStack->getCurrentRequest()->getLocale();
        }
        // If we don't have a request or the current request locale is different from the node langauge
        if ((isset($locale) && $locale !== $lang) || !$this->requestStack->getCurrentRequest()) {
            $request = new Request();
            $request->setLocale($lang);

            $this->router->getContext()->setParameter('_locale', $lang);

            $this->requestStack->push($request);
        }
    }

    /**
     * @return string
     */
    protected function getDefaultSearchViewTemplate()
    {
        return 'KunstmaanNodeSearchBundle:PagePart:view.html.twig';
    }
}
