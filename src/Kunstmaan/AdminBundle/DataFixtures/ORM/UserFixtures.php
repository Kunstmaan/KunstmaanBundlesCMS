<?php

namespace Kunstmaan\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\AdminBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Fixture for creating the admin and guest user
 */
class UserFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user1 = $this->createUser($manager, "admin", "admin", "admin@domain.com", array("ROLE_SUPER_ADMIN"), array($manager->merge($this->getReference('admins-group'))), true);
        $manager->flush();

        $this->setReference('adminuser', $user1);
    }

    /**
     * Create a user
     *
     * @param ObjectManager $manager  The object manager
     * @param string        $username The username
     * @param string        $password The plain password
     * @param string        $email    The email of the user
     * @param array         $roles    The roles the user has
     * @param array         $groups   The groups the user belongs to
     * @param bool          $enabled  Enable login for the user
     *
     * @return User
     */
    private function createUser(ObjectManager $manager, $username, $password, $email, array $roles = array(), array $groups = array(), $enabled = false)
    {
        $user = new User();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setEmail($email);
        $user->setEnabled($enabled);
        foreach ($groups as $group) {
            $user->addGroup($group);
        }

        $manager->persist($user);

        return $user;
    }


    /**
     * Get the order of this fixture
     *
     * @return int
     */
    public function getOrder()
    {
        return 3;
    }

}
