<?php

namespace Kunstmaan\NodeSearchBundle\Services;

use Elastica\Aggregation\Terms;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\PagerFanta\Adapter\SearcherRequestAdapter;
use Kunstmaan\NodeSearchBundle\Search\AbstractElasticaSearcher;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class SearchService
 */
class SearchService
{
    /**
     * @var RenderContext
     */
    protected $renderContext;

    /**
     * @var Container
     */
    protected $container;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var int
     */
    protected $defaultPerPage;

    /**
     * @param ContainerInterface $container
     * @param RequestStack       $requestStack
     * @param int                $defaultPerPage
     */
    public function __construct(ContainerInterface $container, RequestStack $requestStack, $defaultPerPage = 10)
    {
        $this->container = $container;
        $this->requestStack = $requestStack;
        $this->defaultPerPage = $defaultPerPage;
        $this->renderContext = new RenderContext();
    }

    /**
     * @param int $defaultPerPage
     */
    public function setDefaultPerPage($defaultPerPage)
    {
        $this->defaultPerPage = $defaultPerPage;
    }

    /**
     * @return RenderContext
     */
    public function getRenderContext()
    {
        return $this->renderContext;
    }

    /**
     * @param RenderContext $renderContext
     */
    public function setRenderContext($renderContext)
    {
        $this->renderContext = $renderContext;
    }

    /**
     * @return int
     */
    public function getDefaultPerPage()
    {
        return $this->defaultPerPage;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer($container)
    {
        $this->container = $container;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @return Pagerfanta
     */
    public function search()
    {
        $request = $this->requestStack->getCurrentRequest();

        // Retrieve the current page number from the URL, if not present of lower than 1, set it to 1
        $entity = $request->attributes->get('_entity');

        $pageNumber = $this->getRequestedPage($request);
        $searcher = $this->container->get($entity->getSearcher());
        $this->applySearchParams($searcher, $request, $this->renderContext);

        $adapter = new SearcherRequestAdapter($searcher);
        $pagerfanta = new Pagerfanta($adapter);

        try {
            $pagerfanta
                ->setMaxPerPage($this->getDefaultPerPage())
                ->setCurrentPage($pageNumber);
        } catch (NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return $pagerfanta;
    }

    /**
     * @param AbstractElasticaSearcher $searcher
     * @param Request                  $request
     * @param RenderContext            $context
     */
    protected function applySearchParams(AbstractElasticaSearcher $searcher, Request $request, RenderContext &$context)
    {
        // Retrieve the search parameters
        $queryString = trim($request->query->get('query'));
        $queryType = $request->query->get('type');
        $lang = $request->getLocale();

        $context['q_query'] = $queryString;
        $context['q_type'] = $queryType;

        $searcher
            ->setData($this->sanitizeSearchQuery($queryString))
            ->setContentType($queryType)
            ->setLanguage($lang);

        $query = $searcher->getQuery();

        // Aggregations
        $termsAggregation = new Terms('type');
        $termsAggregation->setField('type');

        $query->addAggregation($termsAggregation);
    }

    /**
     * Currently we just search for a complete match...
     *
     * @param string $query
     *
     * @return string
     */
    protected function sanitizeSearchQuery($query)
    {
        return '"' . $query . '"';
    }

    /**
     * @param Request $request
     *
     * @return int
     */
    private function getRequestedPage(Request $request)
    {
        $pageNumber = $request->query->getInt('page', 1);
        if (!$pageNumber || $pageNumber < 1) {
            $pageNumber = 1;
        }

        return $pageNumber;
    }
}
