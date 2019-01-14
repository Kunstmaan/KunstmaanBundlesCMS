<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;

// NEXT_MAJOR: BC layer for deprecated RoleInterface on sf 3.4. Remove this if (keep the else) and move the content of the trait back into this class. Trait is final/internal by default so can be removed
if (interface_exists('\Symfony\Component\Security\Core\Role\RoleInterface')) {
    /**
     * Group
     *
     * @ORM\Entity
     * @ORM\Table(name="kuma_groups")
     */
    class Group implements \Symfony\Component\Security\Core\Role\RoleInterface, GroupInterface
    {
        use GroupPropertiesTrait;
    }
} else {
    /**
     * Group
     *
     * @ORM\Entity
     * @ORM\Table(name="kuma_groups")
     */
    class Group implements GroupInterface
    {
        use GroupPropertiesTrait;
    }
}
