<?php

namespace Kunstmaan\AdminBundle\Component\Security\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;

/**
 * Standard Kunstmaan permission map, based on BasicPermissionMap
 */
class PermissionMap implements PermissionMapInterface
{
    const PERMISSION_VIEW        = 'VIEW';
    const PERMISSION_EDIT        = 'EDIT';
    const PERMISSION_CREATE      = 'CREATE';
    const PERMISSION_DELETE      = 'DELETE';
    const PERMISSION_PUBLISH     = 'PUBLISH';
    
    private $map = array(
                    self::PERMISSION_VIEW => array(
                                    MaskBuilder::MASK_VIEW,
                                    MaskBuilder::MASK_EDIT,
                    ),

                    self::PERMISSION_EDIT => array(
                                    MaskBuilder::MASK_EDIT,
                    ),

                    self::PERMISSION_CREATE => array(
                                    MaskBuilder::MASK_CREATE,
                    ),

                    self::PERMISSION_DELETE => array(
                                    MaskBuilder::MASK_DELETE,
                    ),

                    self::PERMISSION_PUBLISH => array(
                                    MaskBuilder::MASK_PUBLISH,
                    ),
    );

    /**
     * {@inheritDoc}
     */
    public function getMasks($permission, $object)
    {
        if (!isset($this->map[$permission])) {
            return null;
        }

        return $this->map[$permission];
    }

    /**
     * {@inheritDoc}
     */
    public function contains($permission)
    {
        return isset($this->map[$permission]);
    }
    
    public function getPossiblePermissions()
    {
        return array_keys($this->map);
    }
}
