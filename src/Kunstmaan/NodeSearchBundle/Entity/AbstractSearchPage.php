<?php

namespace Kunstmaan\NodeSearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\PagerFanta\Adapter\SearcherRequestAdapter;
use Kunstmaan\NodeSearchBundle\Search\AbstractElasticaSearcher;
use Kunstmaan\SearchBundle\Helper\IndexableInterface;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * AbstractSearchPage, extend this class to create your own SearchPage and extend the standard functionality
 */
class AbstractSearchPage extends AbstractPage implements IndexableInterface
{
    /**
     * Default number of search results to show per page (default: 10)
     * @var int
     */
    private $defaultPerPage = 10;

    /**
     * @param ContainerInterface $container
     * @param Request            $request
     * @param RenderContext      $context
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function service(ContainerInterface $container, Request $request, RenderContext $context)
    {
        // Perform a search if there is a queryString available
        if ($request->query->has('query')) {
            $pagerfanta            = $this->search($container, $request, $context);
            $context['pagerfanta'] = $pagerfanta;
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
        $pageNumber = $this->getRequestedPage($request);
        $searcher   = $container->get($this->getSearcher());
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
    }

    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return array();
    }

    /*
     * return string
     */
    public function getDefaultView()
    {
        return 'KunstmaanNodeSearchBundle:AbstractSearchPage:view.html.twig';
    }

    /**
     * @return boolean
     */
    public function isIndexable()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function getSearcher()
    {
        return 'kunstmaan_node_search.search.node';
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
        if (!$pageNumber or $pageNumber < 1) {
            $pageNumber = 1;
        }

        return $pageNumber;
    }
}
