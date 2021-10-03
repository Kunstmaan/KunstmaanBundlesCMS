<?php

namespace Kunstmaan\NodeSearchBundle\Exception;

final class SearcherServiceNotFoundException extends \RuntimeException
{
    public static function create(string $searcher): self
    {
        return new self(sprintf('No searcher service found for "%s"', $searcher));
    }
}
