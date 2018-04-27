<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Swift_Mailer;
use Swift_Message;
use Swift_Mime_Message;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FormMailer
 *
 * @package Kunstmaan\FormBundle\Helper
 */
class FormMailer implements FormMailerInterface
{
    /** @var \Swift_Mailer */
    private $mailer;

    /** @var EngineInterface * */
    private $templating;

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    /**
     * @param Swift_Mailer       $mailer     The mailer service
     * @param EngineInterface    $templating The templating service
     * @param ContainerInterface $container  The container
     */
    public function __construct(Swift_Mailer $mailer, EngineInterface $templating, ContainerInterface $container)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
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
        /* @var $message Swift_Mime_Message */
        $message = Swift_Message::newInstance()->setSubject($subject)->setFrom($from)->setTo($toArr);
        $message->setBody(
            $this->templating->render(
                'KunstmaanFormBundle:Mailer:mail.html.twig',
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
