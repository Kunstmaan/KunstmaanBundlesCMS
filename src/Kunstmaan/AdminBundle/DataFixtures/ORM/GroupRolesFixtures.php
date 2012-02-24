<?php

namespace Kunstmaan\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\AdminBundle\Entity\Role;
use Doctrine\Common\Persistence\ObjectManager;

class GroupRolesFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $grouprole1 = new Role('ROLE_PERMISSIONMANAGER');
        $manager->persist($grouprole1);
        $manager->flush();

        $grouprole2 = new Role('ROLE_ADMIN');
        $manager->persist($grouprole2);
        $manager->flush();

        $grouprole3 = new Role('ROLE_SUPER_ADMIN');
        $manager->persist($grouprole3);
        $manager->flush();

        $grouprole4 = new Role('ROLE_GUEST');
        $manager->persist($grouprole4);
        $manager->flush();

        $this->addReference('permissionmanager-role',   $grouprole1);
        $this->addReference('admin-role',               $grouprole2);
        $this->addReference('superadmin-role',          $grouprole3);
        $this->addReference('guest-role',               $grouprole4);
    }

    public function getOrder()
    {
        return 1;
    }

}