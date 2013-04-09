<?php

namespace Kunstmaan\SearchBundle\Sherlock;

use Kunstmaan\SearchBundle\Search\SearchProviderInterface;
use Sherlock\Sherlock;

/**
 * The Sherlock SearchProvider
 */
class SherlockSearchProvider implements SearchProviderInterface
{
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

    public function document($indexName, $indexType, $doc, $uid)
    {
        $doc = $this->sherlock
            ->document()
            ->index($indexName)
            ->type($indexType)
            ->document($doc, $uid);
        $doc->execute();
    }

    public function delete($name)
    {
        return $this->sherlock->index($name)->delete();
    }

    public function search($indexName, $indexType, $querystring, $json = false)
    {
        $request = $this->sherlock->search();
        if ($json) {
            $query = Sherlock::queryBuilder()->Raw($querystring);
        } else {
            $titleQuery = Sherlock::queryBuilder()->Wildcard()->field("title")->value($querystring);
            $contentQuery = Sherlock::queryBuilder()->Wildcard()->field("content")->value($querystring);

            $query = Sherlock::queryBuilder()->Bool()->should($titleQuery, $contentQuery)->minimum_number_should_match(1);

            $highlight = Sherlock::highlightBuilder()->Highlight()->pre_tags(array("<strong>"))->post_tags(array("</strong>"))->fields(array("content" => array("fragment_size" => 150, "number_of_fragments" => 1)));

            $request->highlight($highlight);
        }

        $request->index($indexName)
            ->type($indexType)
            ->query($query);

        $response = $request->execute();

        return $response->responseData;
    }

    public function getSherlock()
    {
        return  $this->sherlock;
    }

}
