<?php

namespace Kunstmaan\AdminBundle\Service;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Kunstmaan\AdminBundle\Event\ChangePasswordSuccessEvent;
use Kunstmaan\AdminBundle\Event\Events;
use Kunstmaan\AdminBundle\Service\AuthenticationMailer\AuthenticationMailerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class PasswordResetService
{
    /** @var UserManager */
    private $userManager;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var EventDispatcherInterface */
    private $eventDispatcher;
    /** @var AuthenticationMailerInterface */
    private $authenticationMailer;

    public function __construct(UserManager $userManager, UrlGeneratorInterface $urlGenerator, EventDispatcherInterface $eventDispatcher, AuthenticationMailerInterface $authenticationMailer)
    {
        $this->userManager = $userManager;
        $this->urlGenerator = $urlGenerator;
        $this->eventDispatcher = $eventDispatcher;
        $this->authenticationMailer = $authenticationMailer;
    }

    public function processResetRequest(string $email, string $locale): void
    {
        $user = $this->userManager->findUserByUsernameOrEmail($email);

        if (null === $user) {
            throw new UsernameNotFoundException(sprintf('No user not found with identifier "%s"', $email));
        }

        $this->userManager->setResetToken($user);
        $this->authenticationMailer->sendPasswordResetEmail($user, $locale);
    }

    public function resetPassword(UserInterface $user, string $newPassword): Response
    {
        $user->setPlainPassword($newPassword);
        $this->userManager->updatePassword($user);

        $response = new RedirectResponse($this->urlGenerator->generate('KunstmaanAdminBundle_homepage'));
        $this->dispatch(new ChangePasswordSuccessEvent($user, $response), Events::CHANGE_PASSWORD_COMPLETED);

        return $response;
    }

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($this->eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $this->eventDispatcher->dispatch($eventName, $event);
    }
}
