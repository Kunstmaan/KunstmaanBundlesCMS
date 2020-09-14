<?php

namespace Kunstmaan\AdminBundle\Service;

use Kunstmaan\AdminBundle\Entity\UserInterface;

interface PasswordMailerInterface
{
    public function sendPasswordForgotMail(UserInterface  $user, string $locale = 'nl');
}
