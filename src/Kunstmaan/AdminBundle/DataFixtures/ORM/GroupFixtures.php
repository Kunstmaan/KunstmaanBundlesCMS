<?php

namespace Kunstmaan\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\AdminBundle\Entity\Group;

/**
 * Fixture for creating the basic groups
 */
class GroupFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $group1 = $this->createGroup($manager, 'Administrators', array(
            $this->getReference('permissionmanager-role'),
            $this->getReference('admin-role')
        ));

        $group2 = $this->createGroup($manager, 'Guests', array(
            $this->getReference('guest-role')
        ));

        $group3 = $this->createGroup($manager, 'Super administrators', array(
            $this->getReference('permissionmanager-role'),
            $this->getReference('superadmin-role'),
            $this->getReference('admin-role'),
        ));

        $manager->flush();

        $this->addReference('admins-group',       $group1);
        $this->addReference('guests-group',       $group2);
        $this->addReference('superadmins-group',  $group3);
    }

    /**
     * Create a group
     *
     * @param ObjectManager $manager The object manager
     * @param string        $name    The name of the group
     * @param array         $roles   The roles connected to this group
     *
     * @return Group
     */
    private function createGroup(ObjectManager $manager, $name, array $roles = array())
    {
        $group = new Group($name);
        foreach ($roles as $role) {
            $group->addRole($role);
        }
        $manager->persist($group);

        return $group;
    }

    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

}
