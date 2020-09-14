<?php

namespace Kunstmaan\AdminBundle\Service;

use Kunstmaan\AdminBundle\Entity\UserInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use \Swift_Mailer;
use \Swift_Message;

class SwiftMailerPasswordMailerService implements PasswordMailerInterface
{
    /** @var Swift_Mailer */
    private $mailer;

    /** @var Environment */
    private $twig;

    /** @var RouterInterface */
    private $router;

    /** @var String */
    private $from;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(Swift_Mailer $mailer, Environment $twig, RouterInterface $router, TranslatorInterface $translator, string $from)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->router = $router;
        $this->translator = $translator;
        $this->from = $from;
    }

    public function sendPasswordForgotMail(UserInterface $user, string $locale = 'nl')
    {
        $toArr = [$user->getEmail()];
        $confirmationUrl = $this->router->generate('cms_reset_password_confirm', ['token' => $user->getPasswordRequestToken()], RouterInterface::ABSOLUTE_URL);
        $subject = $this->translator->trans('Password reset email', [], null, $locale);

        $message = (new Swift_Message($subject))
            ->setFrom($this->from)
            ->setTo($toArr)
            ->setBody(
                $this->twig->render(
                    '@KunstmaanAdmin/Resetting/email.txt.twig',
                    [
                        'user' => $user,
                        'confirmationUrl' => $confirmationUrl,
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }

}
