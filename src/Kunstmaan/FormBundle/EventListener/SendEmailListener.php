<?php

namespace Kunstmaan\FormBundle\EventListener;

use Kunstmaan\FormBundle\Event\SubmissionEvent;
use Kunstmaan\FormBundle\Helper\FormMailerInterface;

/**
 * An event listener for sending an email after the form submission is completed
 */
class SendEmailListener
{
    /**
     * @var FormMailerInterface
     */
    private $formMailer;

    /**
     * @param FormMailerInterface $formMailer The form Mailer
     */
    public function __construct(FormMailerInterface $formMailer)
    {
        $this->formMailer = $formMailer;
    }

    /**
     * Configure the form submissions link on top of the form in the sub action menu
     */
    public function onSubmission(SubmissionEvent $event)
    {
        $page = $event->getPage();
        $formSubmission = $event->getSubmission();

        $from = $page->getFromEmail();
        $to = $page->getToEmail();
        $subject = $page->getSubject();
        if (!empty($from) && !empty($to) && !empty($subject)) {
            $this->formMailer->sendContactMail($formSubmission, $from, $to, $subject);
        }
    }
}
