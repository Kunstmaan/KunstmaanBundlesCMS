<?php

namespace Kunstmaan\AdminBundle\Service;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class MailerPasswordMailerService implements PasswordMailerInterface
{
    /** @var MailerInterface */
    private $mailer;

    /** @var RouterInterface */
    private $router;

    /** @var Address */
    private $from;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(MailerInterface $mailer, RouterInterface $router, TranslatorInterface $translator, Address $from = null)
    {
        $this->mailer = $mailer;
        $this->router = $router;
        $this->translator = $translator;
        $this->from = $from;
    }

    public function sendPasswordForgotMail(UserInterface $user, string $locale = 'nl')
    {
        $to = $user->getEmail();
        $confirmationUrl = $this->router->generate('cms_reset_password_confirm', ['token' => $user->getConfirmationToken()], RouterInterface::ABSOLUTE_URL);
        $subject = $this->translator->trans('Password reset email', [], null, $locale);

        $email = (new TemplatedEmail())
            ->from($this->from ?: 'kunstmaancms@myproject.dev')
            ->to($to)
            ->subject($subject)
            ->htmlTemplate('@KunstmaanAdmin/Resetting/email.txt.twig')
            ->context([
                'user' => $user,
                'confirmationUrl' => $confirmationUrl,
            ]);

        $this->mailer->send($email);
    }

}
