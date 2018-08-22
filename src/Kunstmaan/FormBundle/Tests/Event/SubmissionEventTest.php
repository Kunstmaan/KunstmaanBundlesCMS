<?php

namespace Tests\Kunstmaan\FormBundle\Event;

use Kunstmaan\FormBundle\Tests\Entity\FormPage;
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
        $page = new FormPage();

        $submission->setId(1);
        $page->setId(2);

        $event = new SubmissionEvent($submission, $page);
        $this->assertInstanceOf(FormSubmission::class, $event->getSubmission());
        $this->assertInstanceOf(FormPage::class, $event->getPage());
        $this->assertEquals(1, $event->getSubmission()->getId());
        $this->assertEquals(2, $event->getPage()->getId());
    }
}
