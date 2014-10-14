<?php

namespace Kunstmaan\AdminBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Kunstmaan\AdminBundle\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Fixture for creating the admin and guest user
 */
class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /**
     * Sets the Container.
     *
     * @param ContainerInterface|null $container A ContainerInterface instance or null
     *
     * @api
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user1 = $this->createUser(
            $manager,
            'admin',
            'admin',
            'admin@domain.com',
            $this->container->getParameter('kunstmaan_admin.default_admin_locale'),
            array('ROLE_SUPER_ADMIN'),
            array($manager->merge($this->getReference('superadmins-group'))),
            true
        );
        $manager->flush();

        $this->setReference('adminuser', $user1);
    }

    /**
     * Create a user
     *
     * @param ObjectManager $manager The object manager
     * @param string        $username The username
     * @param string        $password The plain password
     * @param string        $email The email of the user
     * @param string        $locale The locale (language) of the user
     * @param array         $roles The roles the user has
     * @param array         $groups The groups the user belongs to
     * @param bool          $enabled Enable login for the user
     *
     * @return User
     */
    private function createUser(
        ObjectManager $manager,
        $username,
        $password,
        $email,
        $locale,
        array $roles = array(),
        array $groups = array(),
        $enabled = false
    ) {
        $user = new User();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setEmail($email);
        $user->setEnabled($enabled);
        $user->setAdminLocale($locale);
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
