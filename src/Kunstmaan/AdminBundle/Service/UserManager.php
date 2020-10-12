<?php

namespace Kunstmaan\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

class UserManager
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var string */
    private $class;

    /** @var string */
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory, EntityManagerInterface $em, string $class)
    {
        $this->em = $em;
        $this->class = $class;
        $this->encoderFactory = $encoderFactory;
    }

    public function deleteUser(UserInterface $user)
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function findUsers()
    {
        return $this->getRepository()->findAll();
    }

    protected function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->getClass());
    }

    public function getClass()
    {
        if (false !== strpos($this->class, ':')) {
            $metadata = $this->em->getClassMetadata($this->class);
            $this->class = $metadata->getName();
        }

        return $this->class;
    }

    public function reloadUser(UserInterface $user)
    {
        $this->em->refresh($user);
    }

    public function updateUser(UserInterface $user, $andFlush = true)
    {
        $this->updatePassword($user);

        $this->em->persist($user);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function updatePassword(UserInterface $user)
    {
        $plainPassword = $user->getPlainPassword();

        if (0 === strlen($plainPassword)) {
            return;
        }

        $encoder = $this->encoderFactory->getEncoder($user);

        if ($encoder instanceof BCryptPasswordEncoder) {
            $user->setSalt(null);
        } else {
            $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
            $user->setSalt($salt);
        }

        $hashedPassword = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($hashedPassword);
        $user->eraseCredentials();
    }

    public function createUser()
    {
        $class = $this->getClass();
        $user = new $class();

        return $user;
    }

    public function findUserByUsernameOrEmail($usernameOrEmail)
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function findUserByEmail($email)
    {
        return $this->findUserBy(['email' => $email]);
    }

    public function findUserBy(array $criteria)
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findUserByUsername($username)
    {
        return $this->findUserBy(['username' => $username]);
    }

    public function findUserByConfirmationToken($token)
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }
}
