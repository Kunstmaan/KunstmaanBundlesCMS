<?php

namespace Kunstmaan\FormBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\FormBundle\Event\SubmissionEvent;
use Kunstmaan\FormBundle\Helper\FormMailerInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * An event listener send formsubmissions to the subscriber
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
     *
     * @param SubmissionEvent $event
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
