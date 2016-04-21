<?php

namespace Kunstmaan\AdminBundle\Helper\Security\OAuth;

use Doctrine\ORM\EntityManager;

class OAuthUserCreator
{
    /** @var EntityManager */
    private $em;

    /** @var array */
    private $hostedDomains;

    /** @var string */
    private $userClass;

    /**
     * OAuthUserCreator constructor.
     * @param EntityManager $em
     * @param $hostedDomains
     * @param $userClass
     */
    public function __construct(EntityManager $em, $hostedDomains, $userClass)
    {
        $this->em = $em;
        $this->hostedDomains = $hostedDomains;
        $this->userClass = $userClass;
    }

    /**
     * Returns an implementation of AbstractUser defined by the $userClass parameter.
     * Checks if there already exists an account for the given googleId or email. If yes updates
     * the access levels accordingly and returns that user. If no creates a new user with the
     * configured access levels.
     *
     * @param string email
     * @param string googleId
     *
     * @return AbstractUser Implementation
     */
    public function getOrCreateUser($email, $googleId)
    {
        $user = $this->em->getRepository($this->userClass)
            ->findOneBy(array('googleId' => $googleId));

        if (!$user instanceof User && $this->isConfiguredDomain($email)) {

            $user = $this->em->getRepository($this->userClass)
                ->findOneBy(array('username' => $email));

            if(!$user instanceof User) {
                $user = new $this->userClass;
                $user->setUsername($email);
                $user->setEmail($email);
                $user->setPlainPassword($googleId . $email . time());
                $user->setEnabled(true);
                $user->setLocked(false);
                $user->setAdminLocale('en');
                $user->setPasswordChanged(true);
            }

            foreach ($this->getAccessLevels($email) as $accessLevel) {
                $user->addRole($accessLevel);
            }
            $user->setGoogleId($googleId);

            // Persist
            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }

    /**
     * This method returns the access level coupled with the domain of the given email
     * If the given domain name has not been configured this function will return null
     *
     * @param string email
     *
     * @return string[]|null
     */
    private function getAccessLevels($email)
    {
        foreach ($this->hostedDomains as $hostedDomain) {
            if(preg_match('/'.$hostedDomain['domain_name'].'$/', $email)) {
                return $hostedDomain['access_levels'];
            }
        }

        return null;
    }

    /**
     * This method returns wether a domain for the given email has been configured
     *
     * @param string email
     *
     * @return bool
     */
    private function isConfiguredDomain($email)
    {
        foreach ($this->hostedDomains as $hostedDomain) {
            if(preg_match('/'.$hostedDomain['domain_name'].'$/', $email)) {
                return true;
            }
        }

        return false;
    }
}
