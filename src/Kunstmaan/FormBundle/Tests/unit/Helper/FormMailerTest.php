<?php

namespace Kunstmaan\FormBundle\Tests\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\FormMailer;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class FormMailerTest extends \PHPUnit_Framework_TestCase
{
    public function testSendContactMail()
    {
        $mailer = $this->createMock(\Swift_Mailer::class);
        $twigEngine = $this->createMock(TwigEngine::class);
        $container = $this->createMock(ContainerInterface::class);
        $request = $this->createMock(Request::class);
        $requestStack = $this->createMock(RequestStack::class);

        $mailer->expects($this->once())->method('send');
        $request->expects($this->once())->method('getScheme')->will($this->returnValue('http'));
        $request->expects($this->once())->method('getHttpHost')->will($this->returnValue('example.com'));
        $requestStack->expects($this->once())->method('getCurrentRequest')->will($this->returnValue($request));
        $container->expects($this->once())->method('get')->will($this->returnValue($requestStack));

        $formMailer = new FormMailer($mailer, $twigEngine, $container);

        $formSubmission = $this->createMock(FormSubmission::class);

        $formMailer->sendContactMail($formSubmission, 'from@example.com', 'to@example.com', 'subject');
    }
}
