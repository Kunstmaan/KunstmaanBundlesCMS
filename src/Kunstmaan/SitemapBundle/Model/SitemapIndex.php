<?php

namespace Kunstmaan\SitemapBundle\Model;

final class SitemapIndex
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var \DateTimeInterface|null
     */
    private $lastModified;

    public function __construct(string $url, ?\DateTimeInterface $lastModified = null)
    {
        $this->url = $url;
        $this->lastModified = $lastModified;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLastModified(): ?\DateTimeInterface
    {
        return $this->lastModified;
    }
}
