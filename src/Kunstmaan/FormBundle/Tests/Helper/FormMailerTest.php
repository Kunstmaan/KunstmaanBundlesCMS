<?php

namespace Kunstmaan\FormBundle\Tests\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\FormMailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class FormMailerTest extends TestCase
{
    public function testSendContactMailWithSymfonyMailer()
    {
        $mailer = $this->createMock(MailerInterface::class);
        $mailer->expects($this->once())->method('send');

        $twig = $this->createMock(Environment::class);
        $request = $this->createMock(Request::class);
        $requestStack = $this->createMock(RequestStack::class);

        $mailer->expects($this->once())->method('send');
        $request->expects($this->once())->method('getScheme')->willReturn('http');
        $request->expects($this->once())->method('getHttpHost')->willReturn('example.com');
        $requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);

        $formMailer = new FormMailer($mailer, $twig, $requestStack);

        /** @var FormSubmission $formSubmission */
        $formSubmission = $this->createMock(FormSubmission::class);

        $formMailer->sendContactMail($formSubmission, 'from@example.com', 'to@example.com', 'subject');
    }
}
