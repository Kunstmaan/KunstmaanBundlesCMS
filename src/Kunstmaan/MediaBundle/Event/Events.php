<?php

namespace Kunstmaan\MediaBundle\Event;

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
    const POST_EDIT = 'media.postEdit';

    /**
     * The postCreate event occurs for a given page, after the create method.
     *
     * This is an entity lifecycle event.
     *
     * @var string
     */
    const POST_CREATE = 'media.postCreate';

}
