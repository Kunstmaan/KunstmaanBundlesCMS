<?php

namespace Kunstmaan\FormBundle\Event;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\FormPageInterface;
use Symfony\Contracts\EventDispatcher\Event;

final class SubmissionEvent extends Event
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

    public function getSubmission(): FormSubmission
    {
        return $this->submission;
    }

    public function getPage(): FormPageInterface
    {
        return $this->page;
    }
}
