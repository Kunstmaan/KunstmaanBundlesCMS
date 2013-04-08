<?php

namespace Kunstmaan\SearchBundle\Service;

class IndexerChain {

    private $indexers;

    public function __construct()
    {
        $this->indexers = array();
    }

    public function addIndexer(IndexerInterface $indexer, $alias)
    {
        $this->indexers[$alias] = $indexer;
    }

    public function getIndexer($alias)
    {
        if (array_key_exists($alias, $this->indexers)) {
            return $this->indexers[$alias];
        }
        else {
            return;
        }
    }

    public function getIndexers()
    {
        return $this->indexers;
    }

}