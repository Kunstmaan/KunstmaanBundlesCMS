<?php

namespace Kunstmaan\AdminBundle\Component\Security\Acl\Permission;

use Symfony\Component\Security\Acl\Domain\RoleSecurityIdentity;

class PermissionHelper
{

    public static function getObjectAceIndex($acl, $role)
    {
        $aces = $acl->getObjectAces();
        foreach ($aces as $index => $ace) {
            $securityIdentity = $ace->getSecurityIdentity();
            if ($securityIdentity instanceof RoleSecurityIdentity) {
                if ($securityIdentity->getRole() == $role) {
                    return $index;
                }
            }
        }
        
        return false;
    }

}
