<?php

namespace Kunstmaan\NodeBundle\Event;

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
    const POSTEDIT = 'kunstmaan_node.postEdit';

    /**
     * The adaptForm event occurs when building the form for the node.
     *
     * @var string
     */
    const ADAPT_FORM = 'kunstmaan_node.adaptForm';

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
