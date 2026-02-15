<?php

namespace Kunstmaan\UserManagementBundle\Event;

final class UserEvents
{
    /**
     * This event will be triggered on edit, after finding user
     *
     * @var string
     */
    public const USER_EDIT_INITIALIZE = 'kunstmaan_usermanagement.edit.initialize';

    /**
     * This event will be triggered on delete, after finding user
     *
     * @var string
     */
    public const USER_DELETE_INITIALIZE = 'kunstmaan_usermanagement.delete.initialize';

    /**
     * This event will be triggered after succesfully deleting the user.
     *
     * @var string
     */
    public const AFTER_USER_DELETE = 'kunstmaan_usermanagement.delete.after';
}
