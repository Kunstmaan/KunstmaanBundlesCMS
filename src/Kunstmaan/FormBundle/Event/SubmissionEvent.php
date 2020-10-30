<?php

namespace Kunstmaan\FormBundle\Event;

use Kunstmaan\AdminBundle\Event\BcEvent;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\FormPageInterface;

class SubmissionEvent extends BcEvent
{
    /**
     * @var FormSubmission
     */
    protected $submission;

    /**
     * @var FormPageInterface
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
