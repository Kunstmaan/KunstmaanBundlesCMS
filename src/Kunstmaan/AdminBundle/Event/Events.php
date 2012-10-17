<?php

namespace Kunstmaan\AdminBundle\Event;

/**
 * AdminBundle events
 */
class Events
{

    /**
     * The onDeepClone event occurs for a given entity while it's being deep cloned. here it's possible to set
     * certain fields of the cloned entity before it's being saved
     *
     * @var string
     */
    const DEEP_CLONE  = 'kunstmaan_admin.onDeepClone';

    /**
     * The postDeepClone event occurs for a given entity after it has been deep cloned.
     *
     * @var string
     */
    const POST_DEEP_CLONE = 'kunstmaan_admin.postDeepClone';
}
