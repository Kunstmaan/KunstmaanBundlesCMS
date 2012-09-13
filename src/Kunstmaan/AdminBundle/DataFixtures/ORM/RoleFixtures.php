<?php

namespace Kunstmaan\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\AdminBundle\Entity\Role;
use Doctrine\Common\Persistence\ObjectManager;

class RoleFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $role1 = new Role('ROLE_PERMISSIONMANAGER');
        $manager->persist($role1);
        $manager->flush();

        $role2 = new Role('ROLE_ADMIN');
        $manager->persist($role2);
        $manager->flush();

        $role3 = new Role('ROLE_SUPER_ADMIN');
        $manager->persist($role3);
        $manager->flush();

        $role4 = new Role('ROLE_GUEST');
        $manager->persist($role4);
        $manager->flush();

        $this->addReference('permissionmanager-role',   $role1);
        $this->addReference('admin-role',               $role2);
        $this->addReference('superadmin-role',          $role3);
        $this->addReference('guest-role',               $role4);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 1;
    }

}
