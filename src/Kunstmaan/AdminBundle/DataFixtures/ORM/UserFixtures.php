<?php

namespace Kunstmaan\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\AdminBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user1->setUsername("admin");
        $user1->setPlainPassword("admin");
        $user1->setRoles(array("ROLE_SUPER_ADMIN"));
        $user1->setEmail("admin@domain.com");
        $user1->setEnabled(true);
        $user1->addGroup($manager->merge($this->getReference('admins-group')));

        $manager->persist($user1);
        $manager->flush();
        $this->setReference('adminuser', $user1);

        $user2 = new User();
        $user2->setUsername("guest");
        $user2->setPlainPassword("guest");
        $user2->setRoles(array("ROLE_GUEST"));
        $user2->setEmail("guest@domain.com");
        $user2->setEnabled(false);
        $user2->addGroup($manager->merge($this->getReference('guests-group')));

        $manager->persist($user2);
        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }

}
