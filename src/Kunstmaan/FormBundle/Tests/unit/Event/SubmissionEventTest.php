<?php

namespace Kunstmaan\FormBundle\Tests\Event;

use Codeception\Stub;
use Kunstmaan\FormBundle\Entity\AbstractFormPage;
use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Event\SubmissionEvent;

/**
 * Class SubmissionEventTest
 */
class SubmissionEventTest extends \PHPUnit_Framework_TestCase
{
    public function testEvent()
    {
        $submission = new FormSubmission();
        $page = Stub::makeEmpty(AbstractFormPage::class, [
            'getId' => 2,
        ]);

        $submission->setId(1);

        $event = new SubmissionEvent($submission, $page);
        $this->assertInstanceOf(FormSubmission::class, $event->getSubmission());
        $this->assertEquals($page, $event->getPage());
        $this->assertEquals(1, $event->getSubmission()->getId());
    }
}
