<?php

namespace Kunstmaan\AdminListBundle\Event;

/**
 * Events
 */
class Events {

    /**
     * The addAdminList event occurs for a given adminlist, after it's being created.
     *
     * @var string
     */
    const ADD_ADMINLIST = 'kunstmaan_adminlist.addAdminList';

    /**
     * The preDelete event occurs for a given adminlist, before it's deleted.
     *
     * @var string
     */
    const PRE_DELETE = 'kunstmaan_adminlist.preDelete';

    /**
     * The postPersist event occurs for a given adminlist, before the adminlist is persisted.
     *
     * @var string
     */
    const PRE_PERSIST = 'kunstmaan_adminlist.prePersist';

    /**
     * The postPersist event occurs for a given adminlist, after the adminlist is persisted.
     *
     * @var string
     */
    const POST_PERSIST = 'kunstmaan_adminlist.postPersist';
} 