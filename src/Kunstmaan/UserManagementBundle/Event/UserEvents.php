<?php

namespace Kunstmaan\UserManagementBundle\Event;

class UserEvents
{
    /**
     * This event will be triggered on edit, after finding user
     *
     * @var string
     */
    const USER_EDIT_INITIALIZE = 'kunstmaan_usermanagement.edit.initialize';

    /**
     * This event will be triggered on delete, after finding user
     *
     * @var string
     */
    const USER_DELETE_INITIALIZE = 'kunstmaan_usermanagement.delete.initialize';
}
