<?php

namespace Kunstmaan\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class UserManager
{
    /** @var EntityManagerInterface */
    private $em;
    /** @var string */
    private $class;
    /** @var PasswordHasherFactoryInterface */
    private $encoderFactory;

    public function __construct(PasswordHasherFactoryInterface $hasherFactory, EntityManagerInterface $em, string $class)
    {
        $this->em = $em;
        $this->class = $class;
        $this->encoderFactory = $hasherFactory;
    }

    public function deleteUser(UserInterface $user): void
    {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function findUsers(): array
    {
        return $this->getRepository()->findAll();
    }

    protected function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->getClass());
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function reloadUser(UserInterface $user): void
    {
        $this->em->refresh($user);
    }

    public function updateUser(UserInterface $user, bool $andFlush = true): void
    {
        $this->updatePassword($user, false);

        $this->em->persist($user);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function updatePassword(UserInterface $user, bool $andFlush = true): void
    {
        $plainPassword = $user->getPlainPassword();
        if ($plainPassword === '' || null === $plainPassword) {
            return;
        }

        $hasher = $this->encoderFactory->getPasswordHasher($user);
        $hashedPassword = $hasher->hash($plainPassword);

        $user->setPassword($hashedPassword);
        $user->setPasswordChanged(true);
        $user->setConfirmationToken(null);
        $user->eraseCredentials();

        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function createUser(): UserInterface
    {
        $class = $this->getClass();

        return new $class();
    }

    public function setResetToken(UserInterface $user): void
    {
        $token = bin2hex(random_bytes(32));
        $user->setConfirmationToken($token);

        $this->em->flush();
    }

    public function findUserByUsernameOrEmail($usernameOrEmail): ?UserInterface
    {
        if (preg_match('/^.+\@\S+\.\S+$/', $usernameOrEmail)) {
            $user = $this->findUserByEmail($usernameOrEmail);
            if (null !== $user) {
                return $user;
            }
        }

        return $this->findUserByUsername($usernameOrEmail);
    }

    public function findUserByEmail($email): ?UserInterface
    {
        return $this->findUserBy(['email' => $email]);
    }

    public function findUserByUsername($username): ?UserInterface
    {
        return $this->findUserBy(['username' => $username]);
    }

    public function findUserByConfirmationToken($token): ?UserInterface
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }

    private function findUserBy(array $criteria): ?UserInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }
}
