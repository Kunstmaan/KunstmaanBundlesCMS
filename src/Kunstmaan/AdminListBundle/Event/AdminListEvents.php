<?php

namespace Kunstmaan\AdminListBundle\Event;

final class AdminListEvents
{
    /**
     * When adding, triggered after the form is validated,
     * but before the entity is persisted.
     */
    public const PRE_ADD = 'kunstmaan_admin_list.preAdd';

    /**
     * When adding, triggered after the entity is flushed.
     */
    public const POST_ADD = 'kunstmaan_admin_list.postAdd';

    /**
     * When editing, triggered after the form is validated,
     * but before the entity is persisted.
     */
    public const PRE_EDIT = 'kunstmaan_admin_list.preEdit';

    /**
     * When editing, triggered after the entity is flushed.
     */
    public const POST_EDIT = 'kunstmaan_admin_list.postEdit';

    /**
     * When deleting, triggered before the entity is removed.
     */
    public const PRE_DELETE = 'kunstmaan_admin_list.preDelete';

    /**
     * When deleting, triggered after the remove is flushed.
     */
    public const POST_DELETE = 'kunstmaan_admin_list.postDelete';
}
