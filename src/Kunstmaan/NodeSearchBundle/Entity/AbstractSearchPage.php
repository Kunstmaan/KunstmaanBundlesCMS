<?php

namespace Kunstmaan\NodeSearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\PagerFanta\Adapter\SearcherRequestAdapter;
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
        // Retrieve the current page number from the URL, if not present of lower than 1, set it to 1
        $pageNumber = $request->get('page');
        if (!$pageNumber or $pageNumber < 1) {
            $pageNumber = 1;
        }

        // Retrieve the search parameters
        $queryString = trim($request->get('query'));
        $queryType   = $request->get('queryType');
        $lang        = $request->getLocale();

        // Perform a search if there is a queryString available
        if (!empty($queryString)) {
            $pagerfanta            = $this->search(
                $container,
                $this->sanitizeSearchQuery($queryString),
                $queryType,
                $lang,
                $pageNumber
            );
            $context['q_query']    = $queryString;
            $context['q_type']     = $queryType;
            $context['pagerfanta'] = $pagerfanta;
        }
    }

    /**
     * @param ContainerInterface $container
     * @param string             $queryString
     * @param string             $queryType
     * @param string             $lang
     * @param int                $pageNumber
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Pagerfanta
     */
    public function search(ContainerInterface $container, $queryString, $queryType, $lang, $pageNumber)
    {
        $searcher = $container->get($this->getSearcher());
        $searcher->setData($queryString);
        $searcher->setContentType($queryType);
        $searcher->setLanguage($lang);

        $adapter    = new SearcherRequestAdapter($searcher);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta
            ->setMaxPerPage($this->getDefaultPerPage());
        try {
            $pagerfanta->setCurrentPage($pageNumber);
        } catch (NotValidCurrentPageException $e) {
            throw new NotFoundHttpException();
        }

        return $pagerfanta;
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
}
