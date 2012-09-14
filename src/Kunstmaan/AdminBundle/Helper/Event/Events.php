<?php

namespace Kunstmaan\AdminBundle\Helper\Event;

/**
 * Defines constants used by events in the AdminBundle
 */
class Events
{
    /**
     * The applyAclChangeset event occurs after saving an AclChangeset.
     *
     * @var string
     */
    const APPLY_ACL_CHANGESET = 'kunstmaan_admin.applyAclChangeset';
}
