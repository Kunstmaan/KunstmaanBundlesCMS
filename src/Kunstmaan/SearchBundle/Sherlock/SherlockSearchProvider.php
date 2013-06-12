<?php

namespace Kunstmaan\SearchBundle\Sherlock;

use Kunstmaan\SearchBundle\Provider\SearchProviderInterface;
use Sherlock\common\exceptions\DocumentMissingException;
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

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return "Sherlock";
    }

    /**
     * @inheritdoc
     */
    public function createIndex($name)
    {
        return $this->sherlock->index($name);
    }

    /**
     * @inheritdoc
     */
    public function addDocument($indexName, $indexType, $doc, $uid)
    {
        $doc = $this->sherlock
            ->document()
            ->index($indexName)
            ->type($indexType)
            ->document($doc, $uid);
        $doc->execute();
    }

    /**
     * @inheritdoc
     */
    public function deleteDocument($indexName, $indexType, $uid)
    {
        try {
            $this->sherlock
                ->deleteDocument()
            ->index($indexName)
            ->type($indexType)
            ->document($uid)
            ->execute();
        } catch (DocumentMissingException $e) {
            // Document already not in index anymore
        }
    }

    /**
     * @inheritdoc
     */
    public function deleteIndex($name)
    {
        return $this->sherlock->index($name)->delete();
    }

    /**
     * @inheritdoc
     */
    public function search($indexName, $indexType, $querystring, $json = false, $from = null, $size = null)
    {
        if ($json) {
            $request = $this->sherlock->raw();
            $request->uri($indexName . "/" . $indexType . "/_search")->method("post")->body($querystring);
        } else {
            $request = $this->sherlock->search();
            $titleQuery = Sherlock::queryBuilder()->Match()->field("title")->query($querystring)->fuzziness(0.7);
            $contentQuery = Sherlock::queryBuilder()->Match()->field("content")->query($querystring)->fuzziness(0.7);

            $query = Sherlock::queryBuilder()->Bool()->should($titleQuery, $contentQuery)->minimum_number_should_match(1);

            $highlight = Sherlock::highlightBuilder()->Highlight()->pre_tags(array("<strong>"))->post_tags(array("</strong>"))->fields(array("content" => array("fragment_size" => 150, "number_of_fragments" => 1)));

            $request->highlight($highlight);
            $request->index($indexName)->type($indexType)->query($query);

            if($from){
                $request->from($from);
            }

            if($size){
                $request->size($size);
            }
        }

        $response = $request->execute();

        return $response->responseData;
    }

    /**
     * @return Sherlock
     */
    public function getSherlock()
    {
        return  $this->sherlock;
    }

}
