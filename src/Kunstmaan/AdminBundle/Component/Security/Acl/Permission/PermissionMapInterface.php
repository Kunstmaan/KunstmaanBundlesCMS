<?php

namespace Kunstmaan\AdminBundle\Component\Security\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\PermissionMapInterface as BasePermissionMapInterface;

interface PermissionMapInterface extends BasePermissionMapInterface
{
    /**
     * Returns an array of permissions.
     *
     * @return array
     */
    function getPossiblePermissions();
}
