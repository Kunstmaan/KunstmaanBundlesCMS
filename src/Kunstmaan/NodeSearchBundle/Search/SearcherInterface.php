<?php

namespace Kunstmaan\NodeSearchBundle\Search;

interface SearcherInterface
{
    /**
     * @param int $offset
     * @param int $size
     *
     * @return \Elastica\ResultSet
     */
    public function search($offset = null, $size = null);

    /**
     * @return \Elastica\ResultSet
     */
    public function getSuggestions();

    /**
     * @param mixed  $query
     * @param string $lang
     * @param string $type
     *
     * @return mixed
     */
    public function defineSearch($query, $lang, $type);

    /**
     * @param int $offset
     * @param int $size
     *
     * @return SearcherInterface
     */
    public function setPagination($offset, $size);

    /**
     * @param mixed $data
     *
     * @return SearcherInterface
     */
    public function setData($data);

    /**
     * @return mixed
     *
     * @return SearcherInterface
     */
    public function getData();

    /**
     * @param string $language
     *
     * @return SearcherInterface
     */
    public function setLanguage($language);

    /**
     * @return string
     */
    public function getLanguage();

    /**
     * @param string $contentType
     *
     * @return SearcherInterface
     */
    public function setContentType($contentType);

    /**
     * @return string
     */
    public function getContentType();

    /**
     * @param string $indexName
     *
     * @return SearcherInterface
     */
    public function setIndexName($name);

    /**
     * @return string
     */
    public function getIndexName();

    /**
     * @param string $indexType
     *
     * @return SearcherInterface
     */
    public function setIndexType($indexType);

    /**
     * @return string
     */
    public function getIndexType();

    /**
     * @param \Kunstmaan\SearchBundle\Search\Search $search
     */
    public function setSearch($search);

    /**
     * @return \Kunstmaan\SearchBundle\Search\Search
     */
    public function getSearch();

    /**
     * @return \Elastica\Query
     */
    public function getQuery();
}
