<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

/**
 * The form mailer
 */
class FormMailer implements FormMailerInterface
{
    /** @var \Swift_Mailer|MailerInterface */
    private $mailer;

    /** @var Environment */
    private $twig;

    /** @var RequestStack */
    private $requestStack;

    public function __construct($mailer, Environment $twig, RequestStack $requestStack)
    {
        if ($mailer instanceof \Swift_Mailer) {
            trigger_deprecation('kunstmaan/form-bundle', '6.3', 'Passing a "\Swift_Mailer" instance for the first parameter in "%s" is deprecated and a Symfony mailer instance will be required in 7.0.', __METHOD__);
        }

        if (!$mailer instanceof \Swift_Mailer && !$mailer instanceof MailerInterface) {
            throw new \TypeError(\sprintf('Argument 1 passed to "%s()" must be an instance of "%s" or "%s", "%s" given.', __METHOD__, \Swift_Mailer::class, MailerInterface::class, \is_object($mailer) ? \get_class($mailer) : \gettype($mailer)));
        }

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

        if ($this->mailer instanceof \Swift_Mailer) {
            // NEXT_MAJOR: Remove swiftmailer code
            $message = (new \Swift_Message($subject))
                ->setFrom($from)
                ->setTo($toArr)
                ->setBody(
                    $this->twig->render(
                        '@KunstmaanForm/Mailer/mail.html.twig',
                        [
                            'submission' => $submission,
                            'host' => $request->getScheme() . '://' . $request->getHttpHost(),
                        ]
                    ),
                    'text/html'
                );
        } else {
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
        }
        $this->mailer->send($message);
    }
}
