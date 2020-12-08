<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\EngineInterface;
use Twig\Environment;

/**
 * The form mailer
 */
class FormMailer implements FormMailerInterface
{
    /** @var \Swift_Mailer */
    private $mailer;

    /** @var EngineInterface|Environment */
    private $twig;

    /** @var RequestStack */
    private $requestStack;

    /**
     * @param EngineInterface                 $twig
     * @param ContainerInterface|RequestStack $requestStack
     */
    public function __construct(Swift_Mailer $mailer, /*Environment*/ $twig, /*RequestStack*/ $requestStack)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;

        if ($twig instanceof EngineInterface) {
            @trigger_error('Passing the "@templating" service as the 2nd argument is deprecated since KunstmaanFormBundle 5.4 and will be replaced by the Twig service in KunstmaanFormBundle 6.0. Injected the "@twig" service instead.', E_USER_DEPRECATED);
        }

        $this->requestStack = $requestStack;
        if ($requestStack instanceof ContainerInterface) {
            @trigger_error('Passing the container as the 3th argument is deprecated since KunstmaanFormBundle 5.4 and will be replaced by the "request_stack" service in KunstmaanFormBundle 6.0. Injected the "@request_stack" service instead.', E_USER_DEPRECATED);

            $this->requestStack = $requestStack->get('request_stack');
        }
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

        $message = (new Swift_Message($subject))
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
        $this->mailer->send($message);
    }
}
