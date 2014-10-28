<?php

namespace Kunstmaan\NodeSearchBundle\Search;

use Elastica\Query;
use Elastica\Search;
use Elastica\Suggest;
use Kunstmaan\SearchBundle\Search\Search as SearchLayer;

abstract class AbstractElasticaSearcher implements SearcherInterface
{
    /**
     * @var string
     */
    protected $indexName;

    /**
     * @var string
     */
    protected $indexType;

    /**
     * @var SearchLayer
     */
    protected $search;

    /**
     * @var Query
     */
    protected $query;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var string
     */
    protected $language;

    /**
     * @var string
     */
    protected $contentType;

    public function __construct()
    {
        $this->query = new Query();
    }

    /**
     * @param mixed  $query
     * @param string $lang
     * @param string $contentType
     *
     * @return mixed
     */
    abstract public function defineSearch($query, $lang, $contentType);

    /**
     * @param int $offset
     * @param int $size
     *
     * @return \Elastica\ResultSet
     */
    public function search($offset = null, $size = null)
    {
        $this->defineSearch($this->data, $this->language, $this->contentType);
        $this->setPagination($offset, $size);

        return $this->getSearchResult();
    }

    /**
     * @return \Elastica\ResultSet
     */
    public function getSuggestions()
    {
        $suggestPhrase = new Suggest\Phrase('content-suggester', 'content');
        $suggestPhrase->setText($this->data);
        $suggestPhrase->setAnalyzer('suggestion_analyzer_' . $this->language);
        $suggestPhrase->setHighlight("<strong>", "</strong>");
        $suggestPhrase->setConfidence(2);
        $suggestPhrase->setSize(1);

        $suggest = new Suggest($suggestPhrase);
        $this->query->setSuggest($suggest);

        return $this->getSearchResult();
    }

    /**
     * @return \Elastica\ResultSet
     */
    public function getSearchResult()
    {
        $index = $this->search->getIndex($this->getIndexName());

        $search = new Search($this->search->getClient());
        $search->addIndex($index);
        $search->addType($index->getType($this->indexType . '_' . $this->language));
        $result = $search->search($this->query);

        return $result;
    }

    /**
     * @param int $offset
     * @param int $size
     *
     * @return SearcherInterface
     */
    public function setPagination($offset, $size)
    {
        if (is_int($offset)) {
            $this->query->setFrom($offset);
        }

        if (is_int($size)) {
            $this->query->setSize($size);
        }

        return $this;
    }

    /**
     * @param string $indexName
     *
     * @return SearcherInterface
     */
    public function setIndexName($indexName)
    {
        $this->indexName = $indexName;

        return $this;
    }

    /**
     * @return string
     */
    public function getIndexName()
    {
        return $this->indexName;
    }

    /**
     * @param string $indexType
     *
     * @return SearcherInterface
     */
    public function setIndexType($indexType)
    {
        $this->indexType = $indexType;

        return $this;
    }

    /**
     * @return string
     */
    public function getIndexType()
    {
        return $this->indexType;
    }

    /**
     * @param mixed $data
     *
     * @return SearcherInterface
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return mixed
     *
     * @return SearcherInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $language
     *
     * @return SearcherInterface
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $contentType
     *
     * @return SearcherInterface
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param SearchLayer $search
     *
     * @return SearcherInterface
     */
    public function setSearch($search)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @return SearchLayer
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }
}
