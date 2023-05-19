<?php

namespace Kunstmaan\FormBundle\Tests\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\FormMailer;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class FormMailerTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testSendContactMailWithSwiftmailer()
    {
        $this->expectDeprecation('Since kunstmaan/form-bundle 6.3: Passing a "\Swift_Mailer" instance for the first parameter in "Kunstmaan\FormBundle\Helper\FormMailer::__construct" is deprecated and a Symfony mailer instance will be required in 7.0.');

        $mailer = $this->createMock(\Swift_Mailer::class);
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

    /**
     * @group legacy
     */
    public function testSendContactMailWithInvalidMailer()
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Argument 1 passed to "Kunstmaan\FormBundle\Helper\FormMailer::__construct()" must be an instance of "Swift_Mailer" or "Symfony\Component\Mailer\MailerInterface", "stdClass" given.');

        new FormMailer(new \stdClass(), $this->createMock(Environment::class), new RequestStack());
    }

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
