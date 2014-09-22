<?php

namespace Kunstmaan\NodeSearchBundle\Search;

interface SearcherInterface
{
    public function search($offset = null, $size = null);

    public function getSuggestions();

    public function defineSearch($query, $lang, $type);

    public function setPagination($offset, $size);

    public function setData($data);

    public function setLanguage($lang);

    public function setContentType($contentType);

    public function setIndexName($name);
}