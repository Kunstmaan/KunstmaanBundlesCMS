<?php

namespace Kunstmaan\AdminBundle\Event;

/**
 * Defines constants used by events in the AdminBundle
 */
class Events
{
    /**
     * The APPLY_ACL_CHANGESET event will be triggered after saving the {@link AclChangeset}
     *
     * @see PermissionAdmin::bindRequest()
     *
     * @var string
     */
    const APPLY_ACL_CHANGESET = 'kunstmaan_admin.applyAclChangeset';
}
