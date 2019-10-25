<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Swift_Mailer;
use Swift_Message;
use Symfony\Component\DependencyInjection\ContainerInterface;
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

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    /**
     * @param Swift_Mailer       $mailer    The mailer service
     * @param EngineInterface    $twig      The templating service
     * @param ContainerInterface $container The container
     */
    public function __construct(Swift_Mailer $mailer, /*Environment*/ $twig, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;

        if ($twig instanceof EngineInterface) {
            @trigger_error('Passing the "@templating" service as the 2nd argument is deprecated since KunstmaanFormBundle 5.4 and will be replaced by the Twig service in KunstmaanFormBundle 6.0. Injected the "@twig" service instead.', E_USER_DEPRECATED);
        }

        $this->container = $container;
    }

    /**
     * @param FormSubmission $submission The submission
     * @param string         $from       The from address
     * @param string         $to         The to address(es) seperated by \n
     * @param string         $subject    The subject
     */
    public function sendContactMail(FormSubmission $submission, $from, $to, $subject)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        $toArr = explode("\r\n", $to);

        $message = (new Swift_Message($subject))
            ->setFrom($from)
            ->setTo($toArr)
            ->setBody(
                $this->twig->render(
                    '@KunstmaanForm/Mailer/mail.html.twig',
                    [
                        'submission' => $submission,
                        'host' => $request->getScheme().'://'.$request->getHttpHost(),
                    ]
                ),
                'text/html'
            );
        $this->mailer->send($message);
    }
}
