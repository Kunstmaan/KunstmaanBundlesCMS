<?php

namespace Kunstmaan\SitemapBundle\Model;

class SitemapUrl
{
    /** @var string */
    private $url;

    /** @var \DateTimeImmutable */
    private $lastModified;

    /** @var float */
    private $priority;

    public function __construct(string $url, \DateTimeImmutable $lastModified, float $priority = 0.9)
    {
        if ($priority > 1 || $priority < 0) {
            throw new \InvalidArgumentException(sprintf('A sitemap url priority can\'t be higher than 1 or below 0. Value given "%s"', $priority));
        }

        $this->url = $url;
        $this->lastModified = $lastModified;
        $this->priority = $priority;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getLastModified(): \DateTimeImmutable
    {
        return $this->lastModified;
    }

    public function getPriority(): float
    {
        return $this->priority;
    }
}
