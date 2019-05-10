<?php

namespace Kunstmaan\FormBundle\Tests\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\FormMailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Templating\DelegatingEngine;

class FormMailerTest extends TestCase
{
    public function testSendContactMail()
    {
        /** @var \Swift_Mailer $mailer */
        $mailer = $this->createMock(\Swift_Mailer::class);
        /** @var DelegatingEngine $twigEngine */
        $twigEngine = $this->createMock(DelegatingEngine::class);
        /** @var ContainerInterface $container */
        $container = $this->createMock(ContainerInterface::class);
        $request = $this->createMock(Request::class);
        $requestStack = $this->createMock(RequestStack::class);

        $mailer->expects($this->once())->method('send');
        $request->expects($this->once())->method('getScheme')->willReturn('http');
        $request->expects($this->once())->method('getHttpHost')->willReturn('example.com');
        $requestStack->expects($this->once())->method('getCurrentRequest')->willReturn($request);
        $container->expects($this->once())->method('get')->willReturn($requestStack);

        $formMailer = new FormMailer($mailer, $twigEngine, $container);

        /** @var FormSubmission $formSubmission */
        $formSubmission = $this->createMock(FormSubmission::class);

        $formMailer->sendContactMail($formSubmission, 'from@example.com', 'to@example.com', 'subject');
    }
}
