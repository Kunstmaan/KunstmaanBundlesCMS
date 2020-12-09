<?php

namespace Kunstmaan\AdminBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\SelfSaltingEncoderInterface;

class UserManager
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var string */
    private $class;

    /** @var string */
    private $encoderFactory;

    public function __construct(EncoderFactoryInterface $encoderFactory, EntityManagerInterface $em, string $class)
    {
        if (false !== strpos($this->class, ':')) {
            @trigger_error(sprintf('Passing a string with the doctrine colon entity notation as "$class" in "%s"is deprecated since KunstmaanAdminBundle 5.8 and will be removed in KunstmaanAdminBundle 6.0. Pass a FQCN for the user class instead.', __CLASS__), E_USER_DEPRECATED);
        }

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
        //NEXT_MAJOR: remove support for xx:xx user class notation.
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
        //TODO: best setup for save user and change password
        $this->updatePassword($user);

        $this->em->persist($user);
        if ($andFlush) {
            $this->em->flush();
        }
    }

    public function updatePassword(UserInterface $user)
    {
        //TODO: best setup for save user and change password
        $plainPassword = $user->getPlainPassword();
        if ($plainPassword === '') {
            return;
        }

        $encoder = $this->encoderFactory->getEncoder($user);
        if ($encoder instanceof SelfSaltingEncoderInterface) {
            $user->setSalt(null);
        } else {
            @trigger_error(sprintf('Using a password encoder requiring a salt is deprecated since KunstmaanAdminBundle 5.8 and will be removed in KunstmaanAdminBundle 6.0. Use a password encoder that implements the "%s" interface instead. ', SelfSaltingEncoderInterface::class), E_USER_DEPRECATED);

            $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
            $user->setSalt($salt);
        }

        $hashedPassword = $encoder->encodePassword($plainPassword, $user->getSalt());
        $user->setPassword($hashedPassword);
        $user->setPasswordChanged(true);
        $user->setConfirmationToken(null);
        $user->eraseCredentials();

        $this->em->flush();
    }

    public function createUser()
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

    public function findUserBy(array $criteria): ?UserInterface
    {
        return $this->getRepository()->findOneBy($criteria);
    }

    public function findUserByUsername($username): ?UserInterface
    {
        return $this->findUserBy(['username' => $username]);
    }

    public function findUserByConfirmationToken($token): ?UserInterface
    {
        return $this->findUserBy(['confirmationToken' => $token]);
    }
}
