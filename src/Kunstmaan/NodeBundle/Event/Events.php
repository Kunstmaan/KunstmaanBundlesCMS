<?php

namespace Kunstmaan\NodeBundle\Event;

/**
 * Events
 */
class Events
{

    /**
     * The preDelete event occurs for a given page, before it's deleted.
     *
     * @var string
     */
    const PRE_DELETE = 'kunstmaan_node.preDelete';

    /**
     * The postDelete event occurs for a given page, after it's deleted.
     *
     * @var string
     */
    const POST_DELETE = 'kunstmaan_node.postDelete';

    /**
     * The adaptForm event occurs when building the form for the node.
     *
     * @var string
     */
    const ADAPT_FORM = 'kunstmaan_node.adaptForm';

    /**
     * The postPersist event occurs for a given page, after the update method.
     *
     * @var string
     */
    const PRE_PERSIST = 'kunstmaan_node.prePersist';

    /**
     * The postPersist event occurs for a given page, after the update method.
     *
     * @var string
     */
    const POST_PERSIST = 'kunstmaan_node.postPersist';

    /**
     * This event will be triggered when creating the menu for the page sub actions.
     * It is possible to change this menu using this event.
     *
     * @var string
     */
    const CONFIGURE_SUB_ACTION_MENU = 'kunstmaan_node.configureSubActionMenu';

    /**
     * This event will be triggered when creating the menu for the page actions.
     * It is possible to change this menu using this event.
     *
     * @var string
     */
    const CONFIGURE_ACTION_MENU = 'kunstmaan_node.configureActionMenu';

}
