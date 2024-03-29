<?php

namespace Kunstmaan\PagePartBundle\Event;

final class Events
{
    /**
     * The postPersist event occurs for a given pagePart, after the pagePart is persisted.
     *
     * @var string
     */
    const POST_PERSIST = 'kunstmaan_pagepart.postPersist';
}
