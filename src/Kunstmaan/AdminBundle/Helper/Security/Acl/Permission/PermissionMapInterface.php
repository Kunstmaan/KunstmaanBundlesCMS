<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\PermissionMapInterface as BasePermissionMapInterface;

/**
 * Defines a basic Kunstmaan PermissionMap
 */
interface PermissionMapInterface extends BasePermissionMapInterface
{
    /**
     * Returns an array of permissions.
     *
     * @return array
     */
    public function getPossiblePermissions();
}
