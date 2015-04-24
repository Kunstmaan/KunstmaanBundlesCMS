<?php

namespace Kunstmaan\NodeSearchBundle\Controller;

use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\PagerFanta\Adapter\SearcherRequestAdapter;
use Kunstmaan\NodeSearchBundle\Search\AbstractElasticaSearcher;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


/**
 * Class AbstractSearchPageController
 */
class AbstractSearchPageController extends Controller{

    /**
     * Default number of search results to show per page (default: 10)
     * @var int
     */
    private $defaultPerPage = 10;

    /**
     * @param Request $request
     */
    public function serviceAction(Request $request)
    {
        //create the render context
        $renderContext = new RenderContext();

        if ($request->query->has('query')) {
            $pagerfanta            = $this->search($this->container, $request, $renderContext);

            $renderContext['pagerfanta'] = $pagerfanta;

            $request->attributes->set('_renderContext', $renderContext);
        }
    }

    /**
     * @param ContainerInterface $container
     * @param Request            $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Pagerfanta
     */
    public function search(ContainerInterface $container, Request $request, RenderContext $context)
    {
        // Retrieve the current page number from the URL, if not present of lower than 1, set it to 1
        $entity = $request->attributes->get('_entity');

        $pageNumber = $this->getRequestedPage($request);
        $searcher   = $container->get($entity->getSearcher());
        $this->applySearchParams($searcher, $request, $context);

        $adapter    = new SearcherRequestAdapter($searcher);
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
    protected function applySearchParams(AbstractElasticaSearcher $searcher, Request $request, RenderContext $context)
    {
        // Retrieve the search parameters
        $queryString = trim($request->query->get('query'));
        $queryType   = $request->query->get('type');
        $lang        = $request->getLocale();

        $context['q_query'] = $queryString;
        $context['q_type']  = $queryType;

        $searcher
            ->setData($this->sanitizeSearchQuery($queryString))
            ->setContentType($queryType)
            ->setLanguage($lang);


        // Facets
        $query      = $searcher->getQuery();
        $facetTerms = new \Elastica\Facet\Terms('type');

        $facetTerms->setField('type');

        $query->addFacet($facetTerms);
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

    /**
     * @param int $defaultPerPage
     *
     * @return AbstractSearchPage
     */
    public function setDefaultPerPage($defaultPerPage)
    {
        $this->defaultPerPage = $defaultPerPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getDefaultPerPage()
    {
        return $this->defaultPerPage;
    }
}