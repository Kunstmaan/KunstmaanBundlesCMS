<?php

namespace Kunstmaan\FormBundle\Helper;

use Swift_Mailer;
use Swift_Message;
use Swift_Mime_Message;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Kunstmaan\FormBundle\Entity\FormSubmission;

/**
 * The form mailer
 */
class FormMailer implements FormMailerInterface
{
    /** @var \Swift_Mailer */
    private $mailer;

    /** @var \Symfony\Bundle\TwigBundle\TwigEngine */
    private $templating;

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    /**
     * @param Swift_Mailer       $mailer     The mailer service
     * @param TwigEngine         $templating The templating service
     * @param ContainerInterface $container  The container
     */
    public function __construct(Swift_Mailer $mailer, TwigEngine $templating, ContainerInterface $container)
    {
        $this->mailer     = $mailer;
        $this->templating = $templating;
        $this->container  = $container;
    }

    /**
     * @param FormSubmission $submission The submission
     * @param string         $from       The from address
     * @param string         $to         The to address(es) seperated by \n
     * @param string         $subject    The subject
     */
    public function sendContactMail(FormSubmission $submission, $from, $to, $subject)
    {
        $toArr = explode("\r\n", $to);
        /* @var $message Swift_Mime_Message */
        $message = Swift_Message::newInstance()->setSubject($subject)->setFrom($from)->setTo($toArr);
        $message->setBody(
            $this->templating->render(
                'KunstmaanFormBundle:Mailer:mail.html.twig',
                array(
                    'submission' => $submission,
                    'host'       => $this->container->get('request')->getScheme() . '://' . $this->container->get('request')->getHttpHost()
                )
            ),
            'text/html'
        );
        $this->mailer->send($message);
    }
}
