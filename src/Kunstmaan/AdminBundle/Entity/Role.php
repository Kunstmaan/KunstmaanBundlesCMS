<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Role\Role as BaseRole;

// NEXT_MAJOR Remove the RolePropertiesTrait when symfony 4 support is removed
if (class_exists(BaseRole::class)) {
    /**
     * @ORM\Entity
     * @ORM\Table( name="kuma_roles" )
     * @UniqueEntity("role")
     */
    #[ORM\Entity]
    #[ORM\Table(name: 'kuma_roles')]
    #[UniqueEntity('role')]
    class Role extends BaseRole
    {
        use RolePropertiesTrait;
    }
} else {
    /**
     * @ORM\Entity
     * @ORM\Table( name="kuma_roles" )
     * @UniqueEntity("role")
     */
    #[ORM\Entity]
    #[ORM\Table(name: 'kuma_roles')]
    #[UniqueEntity('role')]
    class Role
    {
        use RolePropertiesTrait;
    }
}
