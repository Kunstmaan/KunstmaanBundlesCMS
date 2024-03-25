<?php

namespace Kunstmaan\GeneratorBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Kunstmaan\AdminBundle\Entity\Role;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;

/**
 * Fixture for creation the basic roles
 */
class RoleFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    const REFERENCE_PERMISSIONMANAGER_ROLE = 'permissionmanager-role';
    const REFERENCE_ADMIN_ROLE = 'admin-role';
    const REFERENCE_SUPERADMIN_ROLE = 'superadmin-role';
    const REFERENCE_GUEST_ROLE = 'guest-role';
    const REFERENCE_PUBLIC_ACCESS_ROLE = 'public-role';

    /**
     * Load data fixtures with the passed EntityManager
     */
    public function load(ObjectManager $manager)
    {
        $role1 = $this->createRole($manager, 'ROLE_PERMISSIONMANAGER');
        $role2 = $this->createRole($manager, 'ROLE_ADMIN');
        $role3 = $this->createRole($manager, 'ROLE_SUPER_ADMIN');
        $role4 = $this->createRole($manager, 'IS_AUTHENTICATED_ANONYMOUSLY');
        $role5 = null;
        if (defined(AuthenticatedVoter::PUBLIC_ACCESS)) {
            $role5 = $this->createRole($manager, AuthenticatedVoter::PUBLIC_ACCESS);
        }

        $manager->flush();

        $this->addReference(self::REFERENCE_PERMISSIONMANAGER_ROLE, $role1);
        $this->addReference(self::REFERENCE_ADMIN_ROLE, $role2);
        $this->addReference(self::REFERENCE_SUPERADMIN_ROLE, $role3);
        $this->addReference(self::REFERENCE_GUEST_ROLE, $role4);
        if (null !== $role5) {
            $this->addReference(self::REFERENCE_PUBLIC_ACCESS_ROLE, $role5);
        }
    }

    /**
     * Create a role
     *
     * @param ObjectManager $manager The object manager
     * @param string        $name    The name of the role
     */
    private function createRole(ObjectManager $manager, $name): Role
    {
        $role = new Role($name);
        $manager->persist($role);

        return $role;
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }
}
