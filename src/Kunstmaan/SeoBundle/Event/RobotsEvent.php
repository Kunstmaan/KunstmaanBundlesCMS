<?php

declare(strict_types=1);

namespace Kunstmaan\SeoBundle\Event;

class RobotsEvent
{
    private $content;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
