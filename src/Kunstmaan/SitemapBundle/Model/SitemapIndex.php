<?php

namespace Kunstmaan\SitemapBundle\Model;

class SitemapIndex
{
    private $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
