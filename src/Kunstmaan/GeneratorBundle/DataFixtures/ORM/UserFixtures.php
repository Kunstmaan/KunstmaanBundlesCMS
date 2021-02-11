<?php

namespace Kunstmaan\GeneratorBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\AdminBundle\Entity\User;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Fixture for creating the admin and guest user
 */
class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    const REFERENCE_ADMIN_USER = 'adminuser';

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
     */
    public function load(ObjectManager $manager)
    {
        $password = substr(rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '='), 0, 8);

        $user1 = $this->createUser(
            $manager,
            'admin',
            $password,
            'admin@domain.com',
            $this->container->getParameter('kunstmaan_admin.default_admin_locale'),
            ['ROLE_SUPER_ADMIN'],
            [$manager->merge($this->getReference(GroupFixtures::REFERENCE_SUPERADMINS_GROUP))],
            true,
            false
        );
        $user1->setCreatedBy('CMS installation');
        $manager->flush();

        $output = new ConsoleOutput();
        $output->writeln([
            "<comment>  > User 'admin' created with password '$password'</comment>",
        ]);

        if (Kernel::VERSION_ID < 40000) {
            $file = $this->container->get('kernel')->getProjectDir() . '/app/config/config.yml';
            $contents = file_get_contents($file);
            $contents = str_replace('-adminpwd-', $password, $contents);
            file_put_contents($file, $contents);
        }

        $this->setReference(self::REFERENCE_ADMIN_USER, $user1);
    }

    /**
     * Create a user
     *
     * @param ObjectManager $manager  The object manager
     * @param string        $username The username
     * @param string        $password The plain password
     * @param string        $email    The email of the user
     * @param string        $locale   The locale (language) of the user
     * @param array         $roles    The roles the user has
     * @param array         $groups   The groups the user belongs to
     * @param bool          $enabled  Enable login for the user
     * @param bool          $changed  Disable password changed for the user
     *
     * @return User
     */
    private function createUser(
        ObjectManager $manager,
        $username,
        $password,
        $email,
        $locale,
        array $roles = [],
        array $groups = [],
        $enabled = false,
        $changed = false
    ) {
        $user = $this->container->get('fos_user.user_manager')->createUser();
        $user->setUsername($username);
        $user->setPlainPassword($password);
        $user->setRoles($roles);
        $user->setEmail($email);
        $user->setEnabled($enabled);
        $user->setAdminLocale($locale);
        $user->setPasswordChanged($changed);
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
