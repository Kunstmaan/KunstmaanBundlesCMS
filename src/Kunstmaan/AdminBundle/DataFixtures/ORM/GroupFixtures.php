<?php

namespace Kunstmaan\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\AdminBundle\Entity\Group;
use Doctrine\Common\Persistence\ObjectManager;

class GroupFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $group1 = new Group("Administrators");
        $group1->setName("Administrators");
        $group1->addRole($this->getReference('permissionmanager-role'));
        $group1->addRole($this->getReference('admin-role'));
        $manager->persist($group1);
        $manager->flush();

        $group2 = new Group('Guests');
        $group2->setName("Guests");
        $group2->addRole($this->getReference('guest-role'));
        $manager->persist($group2);
        $manager->flush();

        $this->addReference('admins-group', $group1);
        $this->addReference('guests-group', $group2);
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 2;
    }

}
