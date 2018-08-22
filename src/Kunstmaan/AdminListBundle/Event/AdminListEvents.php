<?php

namespace Kunstmaan\AdminListBundle\Event;

class AdminListEvents
{
    /**
     * When adding, triggered after the form is validated,
     * but before the entity is persisted.
     */
    const PRE_ADD = 'kunstmaan_admin_list.preAdd';

    /**
     * When adding, triggered after the entity is flushed.
     */
    const POST_ADD = 'kunstmaan_admin_list.postAdd';

    /**
     * When editing, triggered after the form is validated,
     * but before the entity is persisted.
     */
    const PRE_EDIT = 'kunstmaan_admin_list.preEdit';

    /**
     * When editing, triggered after the entity is flushed.
     */
    const POST_EDIT = 'kunstmaan_admin_list.postEdit';

    /**
     * When deleting, triggered before the entity is removed.
     */
    const PRE_DELETE = 'kunstmaan_admin_list.preDelete';

    /**
     * When deleting, triggered after the remove is flushed.
     */
    const POST_DELETE = 'kunstmaan_admin_list.postDelete';
}
