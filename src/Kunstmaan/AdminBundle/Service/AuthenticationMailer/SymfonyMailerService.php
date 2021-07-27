<?php

namespace Kunstmaan\AdminBundle\Service\AuthenticationMailer;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class SymfonyMailerService implements AuthenticationMailerInterface
{
    /** @var MailerInterface */
    private $mailer;
    /** @var TranslatorInterface */
    private $translator;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var Address */
    private $from;

    public function __construct(MailerInterface $mailer, TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator, Address $from)
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->from = $from;
    }

    public function sendPasswordResetEmail(UserInterface $user, string $locale): void
    {
        $confirmationUrl = $this->urlGenerator->generate('kunstmaan_admin_reset_password_confirm', ['token' => $user->getConfirmationToken()], RouterInterface::ABSOLUTE_URL);

        $email = (new TemplatedEmail())
            ->from($this->from)
            ->to(new Address($user->getEmail()))
            ->subject($this->translator->trans('security.resetting.mail.subject', [], null, $locale))
            ->htmlTemplate('@KunstmaanAdmin/authentication/email/password_reset.html.twig')
            ->context([
                'user' => $user,
                'confirmationUrl' => $confirmationUrl,
            ]);

        $this->mailer->send($email);
    }
}
