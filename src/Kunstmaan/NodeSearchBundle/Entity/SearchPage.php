<?php

namespace Kunstmaan\NodeSearchBundle\Entity;

use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Kunstmaan\SearchBundle\Helper\IndexControllerInterface;
use Sherlock\Sherlock;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ORM\Entity()
 * @ORM\Table(name="kuma_searchpage")
 */
class SearchPage extends AbstractPage implements IndexControllerInterface {

    public function service(ContainerInterface $container, Request $request, RenderContext $context)
    {
        $querystring = $request->get("query");
        $querytag = $request->get("tag");
        $queryrtag = $request->get("rtag");
        $querytype = $request->get("type");
        $tags = array();
        if($querytag and $querytag != ''){
            $tags = explode(',', $querytag);
            if($queryrtag and $queryrtag != '') {
                unset($tags[$queryrtag]);
                $tags = array_merge(array_diff($tags, array($queryrtag)));
            }
        }
        if ($querystring and $querystring != "") {
            $responseData = $this->search($container, $querystring, $querytype, $tags);

            $context['hits'] = $responseData['hits']['hits'];
            $context['q_query'] = $querystring;
            $context['q_tags'] = implode(',', $tags);
            $context['s_tags'] = $tags;
            $context['q_type'] = $querytype;
            $context['facets'] = $responseData['facets'];
            $context['responseData'] = $responseData;
        }
    }

    public function search($container, $querystring, $type, $tags)
    {
        $search = $container->get('kunstmaan_search.search');
        $sherlock = $container->get('kunstmaan_search.searchprovider.sherlock');
        $request = $sherlock->getSherlock()->search();

        $titleQuery = Sherlock::queryBuilder()->Wildcard()->field("title")->value($querystring);
        $contentQuery = Sherlock::queryBuilder()->Wildcard()->field("content")->value($querystring);

        $query = $tagQuery = Sherlock::queryBuilder()->Bool()->should($titleQuery, $contentQuery)->minimum_number_should_match(1);

        if (count($tags) > 0) {
            $tagQueries = array();
            foreach ($tags as $tag) {
                $tagQueries[] = Sherlock::queryBuilder()->Term()->field("tags")->term($tag);
            }
            $tagQuery = Sherlock::queryBuilder()->Bool()->must($tagQueries)->minimum_number_should_match(1);
            $query = Sherlock::queryBuilder()->Bool()->must(array($tagQuery, $query))->minimum_number_should_match(1);
        }

        if ($type && $type != '') {
            $typeQuery = Sherlock::queryBuilder()->Term()->field("type")->term($type);
            $query = Sherlock::queryBuilder()->Bool()->must(array($typeQuery, $query))->minimum_number_should_match(1);
        }

        $request->query($query);

        $tagFacet = Sherlock::facetBuilder()->Terms()->fields("tags")->facetname("tag");
        $typeFacet = Sherlock::facetBuilder()->Terms()->fields("type")->facetname("type");
        $request->facets($tagFacet, $typeFacet);

        $highlight = Sherlock::highlightBuilder()->Highlight()->pre_tags(array("<strong>"))->post_tags(array("</strong>"))->fields(array("content" => array("fragment_size" => 150, "number_of_fragments" => 1)));

        $request->highlight($highlight);

        $json = $request->toJSON();

        $response = $search->search("nodeindex", "page", $json, true);

        return $response;
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
        return "KunstmaanNodeSearchBundle:SearchPage:view.html.twig";
    }

    /**
     * @return boolean
     */
    public function shouldBeIndexed()
    {
        return false;
    }
}