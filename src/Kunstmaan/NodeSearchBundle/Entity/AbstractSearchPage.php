<?php

namespace Kunstmaan\NodeSearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\NodeSearchBundle\PagerFanta\Adapter\SherlockRequestAdapter;
use Kunstmaan\SearchBundle\Helper\ShouldBeIndexedInterface;
use Pagerfanta\Exception\NotValidCurrentPageException;
use Pagerfanta\Pagerfanta;
use Sherlock\Sherlock;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * AbstractSearchPage, extend this class to create your own SearchPage and extends the standard functionality
 *
 */
class AbstractSearchPage extends AbstractPage implements ShouldBeIndexedInterface
{
    /**
     * Default number of search results to show per page (default: 10)
     * @var int
     */
    public $defaultperpage = 10;

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
        $pagenumber = $request->get("page");
        if (!$pagenumber || $pagenumber < 1) {
            $pagenumber = 1;
        }
        // Retrieve the search parameters
        $querystring = $request->get("query");
        $querytag = $request->get("tag");
        $queryrtag = $request->get("rtag");
        $querytype = $request->get("type");
        $lang = $request->getLocale();
        $tags = array();
        // Put the tags in an array
        if ($querytag && $querytag != '') {
            $tags = explode(',', $querytag);
            if ($queryrtag && $queryrtag != '') {
                unset($tags[$queryrtag]);
                $tags = array_merge(array_diff($tags, array($queryrtag)));
            }
        }
        // Perform a search if there is a querystring available
        if ($querystring && $querystring != "") {
            $pagerfanta = $this->search($container, $querystring, $querytype, $tags, $lang, $pagenumber);
            $context['q_query'] = $querystring;
            $context['q_tags'] = implode(',', $tags);
            $context['s_tags'] = $tags;
            $context['q_type'] = $querytype;
            $context['pagerfanta'] = $pagerfanta;
        }
    }

    /**
     * @param ContainerInterface $container
     * @param string             $querystring
     * @param string             $type
     * @param array              $tags
     * @param string             $lang
     * @param int                $pagenumber
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Pagerfanta
     */
    public function search(ContainerInterface $container, $querystring, $type, array $tags, $lang, $pagenumber)
    {
        $search = $container->get('kunstmaan_search.search');
        $sherlock = $container->get('kunstmaan_search.searchprovider.sherlock');
        $request = $sherlock->getSherlock()->search();

        $titleQuery = Sherlock::queryBuilder()->Match()->field("title")->query($querystring)->fuzziness(0.7);
        $contentQuery = Sherlock::queryBuilder()->Match()->field("content")->query($querystring)->fuzziness(0.7);
        $langQuery = Sherlock::queryBuilder()->Term()->field("lang")->term($lang);

        $querystringQuery = Sherlock::queryBuilder()->Bool()->should($titleQuery, $contentQuery)->minimum_number_should_match(1);
        $query = Sherlock::queryBuilder()->Bool()->must($langQuery, $querystringQuery)->minimum_number_should_match(1);

        if (count($tags) > 0) {
            $tagQueries = array();
            foreach ($tags as $tag) {
                $tagQueries[] = Sherlock::queryBuilder()->Term()->field("tags")->term($tag);
            }
            $tagQuery = Sherlock::queryBuilder()->Bool()->must($tagQueries)->minimum_number_should_match(1);
            $query = Sherlock::queryBuilder()->Bool()->must($tagQuery, $query)->minimum_number_should_match(1);
        }

        if ($type && $type != '') {
            $typeQuery = Sherlock::queryBuilder()->Term()->field("type")->term($type);
            $query = Sherlock::queryBuilder()->Bool()->must($typeQuery, $query)->minimum_number_should_match(1);
        }

        $request->query($query);

        $tagFacet = Sherlock::facetBuilder()->Terms()->fields("tags")->facetname("tag");
        $typeFacet = Sherlock::facetBuilder()->Terms()->fields("type")->facetname("type");
        $request->facets($tagFacet, $typeFacet);

        $highlight = Sherlock::highlightBuilder()->Highlight()->pre_tags(array("<strong>"))->post_tags(array("</strong>"))->fields(array("content" => array("fragment_size" => 250, "number_of_fragments" => 1)));

        $request->highlight($highlight);

        $adapter = new SherlockRequestAdapter($search, "nodeindex", "page", $request);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage($this->defaultperpage);
        try {
            $pagerfanta->setCurrentPage($pagenumber);
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
        return "KunstmaanNodeSearchBundle:AbstractSearchPage:view.html.twig";
    }

    /**
     * @return boolean
     */
    public function shouldBeIndexed()
    {
        return false;
    }
}
