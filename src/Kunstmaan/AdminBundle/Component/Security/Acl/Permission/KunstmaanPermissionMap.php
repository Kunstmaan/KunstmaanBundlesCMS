<?php

namespace Kunstmaan\AdminBundle\Component\Security\Acl\Permission;

use Symfony\Component\Security\Acl\Permission\PermissionMapInterface;

/**
 * Standard Kunstmaan permission map, based on BasicPermissionMap
 */
class KunstmaanPermissionMap implements PermissionMapInterface
{
    const PERMISSION_VIEW        = 'VIEW';
    const PERMISSION_EDIT        = 'EDIT';
    const PERMISSION_CREATE      = 'CREATE';
    const PERMISSION_DELETE      = 'DELETE';
    const PERMISSION_UNDELETE    = 'UNDELETE';
    const PERMISSION_PUBLISH     = 'PUBLISH';
    
    private $map = array(
                    self::PERMISSION_VIEW => array(
                                    KunstmaanMaskBuilder::MASK_VIEW,
                                    KunstmaanMaskBuilder::MASK_EDIT,
                    ),

                    self::PERMISSION_EDIT => array(
                                    KunstmaanMaskBuilder::MASK_EDIT,
                    ),

                    self::PERMISSION_CREATE => array(
                                    KunstmaanMaskBuilder::MASK_CREATE,
                    ),

                    self::PERMISSION_DELETE => array(
                                    KunstmaanMaskBuilder::MASK_DELETE,
                    ),

                    self::PERMISSION_UNDELETE => array(
                                    KunstmaanMaskBuilder::MASK_UNDELETE,
                    ),

                    self::PERMISSION_PUBLISH => array(
                                    KunstmaanMaskBuilder::MASK_PUBLISH,
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
}
