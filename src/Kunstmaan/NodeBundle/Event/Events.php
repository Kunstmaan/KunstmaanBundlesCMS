<?php

namespace Kunstmaan\NodeBundle\Event;

/**
 * Events
 */
class Events
{

    /**
     * The addNode event occurs for a given page, after it's being created.
     *
     * @var string
     */
    const ADD_NODE = 'kunstmaan_node.addNode';

    /**
     * The preUnPublish event occurs for a given page, before it's unpublished.
     *
     * @var string
     */
    const PRE_UNPUBLISH = 'kunstmaan_node.preUnPublish';

    /**
     * The postUnPublish event occurs for a given page, after it's unpublished.
     *
     * @var string
     */
    const POST_UNPUBLISH = 'kunstmaan_node.postUnPublish';

    /**
     * The prePublish event occurs for a given page, before it's published.
     *
     * @var string
     */
    const PRE_PUBLISH = 'kunstmaan_node.prePublish';

    /**
     * The postPublish event occurs for a given page, after it's published.
     *
     * @var string
     */
    const POST_PUBLISH = 'kunstmaan_node.postPublish';

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
     * The copyPageTranslation event occurs for a given page, after a page translation has been copied.
     *
     * @var string
     */
    const COPY_PAGE_TRANSLATION = 'kunstmaan_node.copyPageTranslation';

    /**
     * The emptyPageTranslation event occurs for a given page, after a new page translation is created.
     *
     * @var string
     */
    const ADD_EMPTY_PAGE_TRANSLATION = 'kunstmaan_node.emptyPageTranslation';

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
