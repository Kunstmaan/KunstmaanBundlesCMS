<?php

namespace Kunstmaan\AdminNodeBundle\Helper\Event;

/**
 * Events
 */
class Events
{

    /**
     * The postEdit event occurs for a given page, after the update method.
     *
     * This is an entity lifecycle event.
     *
     * @var string
     */
    const POSTEDIT = 'adminnode.postEdit';

}
