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
     * @param string $type
     */
    public function defineSearch($query, $type);

    /**
     * @param int $offset
     * @param int $size
     *
     * @return SearcherInterface
     */
    public function setPagination($offset, $size);

    /**
     * @return SearcherInterface
     */
    public function setData($data);

    /**
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
     * @return SearcherInterface
     */
    public function setIndexName($name);

    /**
     * @return string
     */
    public function getIndexName();

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
