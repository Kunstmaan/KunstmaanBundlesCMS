<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class FormMailer implements FormMailerInterface
{
    /** @var MailerInterface */
    private $mailer;

    /** @var Environment */
    private $twig;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(MailerInterface $mailer, Environment $twig, RequestStack $requestStack)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->requestStack = $requestStack;
    }

    /**
     * @param FormSubmission $submission The submission
     * @param string         $from       The from address
     * @param string         $to         The to address(es) seperated by \n
     * @param string         $subject    The subject
     */
    public function sendContactMail(FormSubmission $submission, $from, $to, $subject)
    {
        $request = $this->requestStack->getCurrentRequest();

        $toArr = explode("\r\n", $to);

        $message = (new TemplatedEmail())
            ->from($from)
            ->to(...$toArr)
            ->subject($subject)
            ->htmlTemplate('@KunstmaanForm/Mailer/mail.html.twig')
            ->context([
                'submission' => $submission,
                'host' => null !== $request ? $request->getScheme() . '://' . $request->getHttpHost() : '',
            ])
        ;
        $this->mailer->send($message);
    }
}
