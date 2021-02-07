<?php

namespace Kunstmaan\AdminBundle\Service\AuthenticationMailer;

use Kunstmaan\AdminBundle\Entity\UserInterface;

interface AuthenticationMailerInterface
{
    public function sendPasswordResetEmail(UserInterface $user, string $locale): void;
}
