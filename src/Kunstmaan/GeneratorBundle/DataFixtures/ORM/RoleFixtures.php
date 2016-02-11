<?php

namespace Kunstmaan\GeneratorBundle\DataFixtures\ORM;

use Kunstmaan\AdminBundle\Entity\Role;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixture for creation the basic roles
 */
class RoleFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $role1 = $this->createRole($manager, 'ROLE_PERMISSIONMANAGER');
        $role2 = $this->createRole($manager, 'ROLE_ADMIN');
        $role3 = $this->createRole($manager, 'ROLE_SUPER_ADMIN');
        $role4 = $this->createRole($manager, 'IS_AUTHENTICATED_ANONYMOUSLY');

        $manager->flush();

        $this->addReference('permissionmanager-role', $role1);
        $this->addReference('admin-role', $role2);
        $this->addReference('superadmin-role', $role3);
        $this->addReference('guest-role', $role4);
    }

    /**
     * Create a role
     *
     * @param ObjectManager $manager The object manager
     * @param string        $name    The name of the role
     *
     * @return Role
     */
    private function createRole(ObjectManager $manager, $name)
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
