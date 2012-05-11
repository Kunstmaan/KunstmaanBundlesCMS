<?php

namespace Kunstmaan\FormBundle\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;

use Symfony\Bundle\TwigBundle\TwigEngine;

/**
 * The form mailer
 */
class Mailer
{

    private $mailer;
    private $templating;

    /**
     * @param \Swift_Mailer $mailer     The mailer service
     * @param TwigEngine    $templating The templating service
     */
    public function __construct($mailer, $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
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
        $message = \Swift_Message::newInstance()->setSubject($subject)->setFrom($from)->setTo($toArr)
                ->setBody($this->templating->render('KunstmaanFormBundle:Mailer:mail.html.twig', array('submission' => $submission)), 'text/html');
        $this->mailer->send($message);
    }
}
