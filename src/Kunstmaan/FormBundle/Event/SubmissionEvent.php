<?php

namespace Kunstmaan\FormBundle\Event;

use Kunstmaan\AdminBundle\Event\BcEvent;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\FormPageInterface;

/**
 * @final since 5.9
 */
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
