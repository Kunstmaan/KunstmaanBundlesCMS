<?php

namespace Kunstmaan\MediaBundle\Helper\Event;

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
    const postEdit = 'media.postEdit';

    /**
     * The postCreate event occurs for a given page, after the create method.
     *
     * This is an entity lifecycle event.
     *
     * @var string
     */
    const postCreate = 'media.postCreate';

}
