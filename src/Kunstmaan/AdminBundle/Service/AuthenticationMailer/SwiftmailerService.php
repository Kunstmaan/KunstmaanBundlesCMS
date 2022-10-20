<?php

namespace Kunstmaan\AdminBundle\Service\AuthenticationMailer;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

/**
 * @deprecated since 6.3, use \Kunstmaan\AdminBundle\Service\AuthenticationMailer\SymfonyMailerService instead.
 * NEXT_MAJOR remove swiftmailer composer dependency.
 */
final class SwiftmailerService implements AuthenticationMailerInterface
{
    /** @var \Swift_Mailer */
    private $mailer;
    /** @var Environment */
    private $twig;
    /** @var TranslatorInterface */
    private $translator;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var string */
    private $senderName;
    /** @var string */
    private $senderAddress;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, TranslatorInterface $translator, UrlGeneratorInterface $urlGenerator, string $senderAddress, string $senderName)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->translator = $translator;
        $this->urlGenerator = $urlGenerator;
        $this->senderAddress = $senderAddress;
        $this->senderName = $senderName;
    }

    public function sendPasswordResetEmail(UserInterface $user, string $locale): void
    {
        $confirmationUrl = $this->urlGenerator->generate('kunstmaan_admin_reset_password_confirm', ['token' => $user->getConfirmationToken()], RouterInterface::ABSOLUTE_URL);
        $body = $this->twig->render('@KunstmaanAdmin/authentication/email/password_reset.html.twig', [
            'user' => $user,
            'confirmationUrl' => $confirmationUrl,
        ]);

        $message = (new \Swift_Message())
            ->setSubject($this->translator->trans('security.resetting.mail.subject', [], null, $locale))
            ->setFrom($this->senderAddress, $this->senderName)
            ->setTo($user->getEmail())
            ->setBody($body, 'text/html')
        ;

        $this->mailer->send($message);
    }
}
