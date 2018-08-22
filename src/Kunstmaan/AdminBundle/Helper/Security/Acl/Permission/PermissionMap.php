<?php

namespace Kunstmaan\AdminBundle\Helper\Security\Acl\Permission;

use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMapInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilderInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilderRetrievalInterface;

/**
 * PermissionMap which stores all the possible permissions, this is based on the BasicPermissionMap
 */
class PermissionMap implements PermissionMapInterface, MaskBuilderRetrievalInterface
{

    const PERMISSION_VIEW       = 'VIEW';
    const PERMISSION_EDIT       = 'EDIT';
    const PERMISSION_DELETE     = 'DELETE';
    const PERMISSION_PUBLISH    = 'PUBLISH';
    const PERMISSION_UNPUBLISH  = 'UNPUBLISH';

    private $map = array(
        self::PERMISSION_VIEW    => array(
            MaskBuilder::MASK_VIEW,
        ),

        self::PERMISSION_EDIT    => array(
            MaskBuilder::MASK_EDIT,
        ),
        self::PERMISSION_DELETE  => array(
            MaskBuilder::MASK_DELETE,
        ),

        self::PERMISSION_PUBLISH => array(
            MaskBuilder::MASK_PUBLISH,
        ),

        self::PERMISSION_UNPUBLISH => array(
            MaskBuilder::MASK_UNPUBLISH,
        ),
    );

    /**
     * Returns an array of bitmasks.
     *
     * The security identity must have been granted access to at least one of
     * these bitmasks.
     *
     * @param string      $permission The permission
     * @param object|null $object     The object
     *
     * @return array may return null if permission/object combination is not supported
     */
    public function getMasks($permission, $object)
    {
        if (!isset($this->map[$permission])) {
            return null;
        }

        return $this->map[$permission];
    }

    /**
     * Whether this map contains the given permission
     *
     * @param string $permission
     *
     * @return bool
     */
    public function contains($permission)
    {
        return isset($this->map[$permission]);
    }

    /**
     * Returns the array of permissions.
     *
     * @return array
     */
    public function getPossiblePermissions()
    {
        return array_keys($this->map);
    }

    /**
     * Returns a new instance of the MaskBuilder used in the permissionMap.
     *
     * @return MaskBuilderInterface
     */
    public function getMaskBuilder()
    {
        return new MaskBuilder();
    }
}
