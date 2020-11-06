<?php

namespace Kunstmaan\AdminListBundle\Tests\Event;

use DateTime;
use Kunstmaan\AdminListBundle\Event\AdminListEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminListEventTest extends TestCase
{
    public function testGetSet()
    {
        $date = new DateTime();
        $request = new Request();
        $form = $this->createMock(Form::class);

        $event = new AdminListEvent($date, $request, $form);

        $this->assertInstanceOf(Form::class, $event->getForm());
        $this->assertInstanceOf(DateTime::class, $event->getEntity());
        $this->assertInstanceOf(Request::class, $event->getRequest());

        $event->setResponse(new Response());

        $this->assertInstanceOf(Response::class, $event->getResponse());
        $this->assertTrue($event->isPropagationStopped());
    }
}
