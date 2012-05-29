<?php

namespace Kunstmaan\AdminNodeBundle\Helper\Event;

class Events
{

    private function __construct() {}
    /**
     * The postEdit event occurs for a given page, after the update method.
     *
     * This is an entity lifecycle event.
     *
     * @var string
     */
    const postEdit = 'adminnode.postEdit';

}
