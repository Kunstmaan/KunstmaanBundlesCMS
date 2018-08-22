<?php

namespace Kunstmaan\FormBundle\Event;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Symfony\Component\EventDispatcher\Event;

class SubmissionEvent extends Event
{
    /**
     * @var FormSubmission
     */
    protected $submission;

    /**
     * @var AbstractPage
     */
    protected $page;

    /**
     * @param FormSubmission $submission
     * @param AbstractPage $page
     */
    public function __construct(FormSubmission $submission, AbstractPage $page)
    {
        $this->submission = $submission;
        $this->page = $page;
    }

    /**
     * @return FormSubmission
     */
    public function getSubmission()
    {
        return $this->submission;
    }

    /**
     * @return AbstractPage
     */
    public function getPage()
    {
        return $this->page;
    }
}
