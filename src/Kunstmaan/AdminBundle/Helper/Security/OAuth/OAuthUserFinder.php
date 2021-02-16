<?php

namespace Kunstmaan\AdminBundle\Helper\Security\OAuth;

use Doctrine\ORM\EntityManagerInterface;

class OAuthUserFinder implements OAuthUserFinderInterface
{
    /** @var EntityManager */
    private $em;

    /** @var string */
    private $userClass;

    public function __construct(EntityManagerInterface $em, $userClass)
    {
        $this->em = $em;
        $this->userClass = $userClass;
    }

    public function findUserByGoogleSignInData($email, $googleId)
    {
        //Check if already logged in before via Google auth
        $user = $this->em->getRepository($this->userClass)
            ->findOneBy(['googleId' => $googleId]);

        if (!$user instanceof $this->userClass) {
            //Check if Email was already present in database but not logged in via Google auth
            $user = $this->em->getRepository($this->userClass)
                ->findOneBy(['email' => $email]);

            if (!$user instanceof $this->userClass) {
                //Last chance try looking for email address in username field
                $user = $this->em->getRepository($this->userClass)
                    ->findOneBy(['username' => $email]);
            }
        }

        return $user;
    }
}
