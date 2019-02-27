<?php

namespace Kunstmaan\FormBundle\Event;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\FormPageInterface;
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
     * @param FormSubmission    $submission
     * @param FormPageInterface $page
     */
    public function __construct(FormSubmission $submission, FormPageInterface $page)
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
     * @return FormPageInterface
     */
    public function getPage()
    {
        return $this->page;
    }
}
