<?php

namespace Kunstmaan\SearchBundle\Sherlock;

use Kunstmaan\SearchBundle\Search\SearchProviderInterface;
use Sherlock\Sherlock;

class SherlockImpl implements SearchProviderInterface {

    private $sherlock;

    public function __construct($hostname, $port)
    {
        $this->sherlock = new Sherlock;
        $this->sherlock->addNode($hostname, $port);
    }

    public function getName()
    {
        return "Sherlock";
    }

    public function index($name)
    {
        return $this->sherlock->index($name);
    }

    public function document($indexName, $indexType, $doc )
    {
        $doc = $this->sherlock
            ->document()
            ->index($indexName)
            ->type($indexType)
            ->document($doc);
        $doc->execute();
    }

    public function delete($name)
    {
        return $this->sherlock->index($name)->delete();
    }

    public function search($querystring, $type = array(), $tags = array())
    {
        $request = $this->sherlock->search();

        $titleQuery = Sherlock::queryBuilder()->Wildcard()->field("title")->value($querystring);
        $contentQuery = Sherlock::queryBuilder()->Wildcard()->field("content")->value($querystring);

        $query = $tagQuery = Sherlock::queryBuilder()->Bool()->should($titleQuery, $contentQuery)->minimum_number_should_match(1);

        if(count($tags) > 0){
            $tagQueries = array();
            foreach($tags as $tag){
                $tagQueries[] = Sherlock::queryBuilder()->Term()->field("tags")->term($tag);
            }
            $tagQuery = Sherlock::queryBuilder()->Bool()->must($tagQueries)->minimum_number_should_match(1);
            $query = Sherlock::queryBuilder()->Bool()->must(array($tagQuery, $query))->minimum_number_should_match(1);
        }

        if($type && $type != ''){
            $typeQuery = Sherlock::queryBuilder()->Term()->field("type")->term($type);
            $query = Sherlock::queryBuilder()->Bool()->must(array($typeQuery, $query))->minimum_number_should_match(1);
        }

        $request->index("nodeindex")
                ->type("page")
                ->query($query);

        $tagFacet = Sherlock::facetBuilder()->Terms()->fields("tags")->facetname("tag");
        $typeFacet = Sherlock::facetBuilder()->Terms()->fields("type")->facetname("type");
        $request->facets($tagFacet, $typeFacet);

        $highlight = Sherlock::highlightBuilder()->Highlight()->pre_tags(array("<strong>"))->post_tags(array("</strong>"))->fields(array("content" => array("fragment_size" => 150, "number_of_fragments" => 1)));

        $request->highlight($highlight);

        $response = $request->execute();

        return $response;
    }


}