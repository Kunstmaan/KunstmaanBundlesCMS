<?php

namespace Kunstmaan\FormBundle\Tests\Helper;

use Kunstmaan\FormBundle\Entity\FormSubmission;
use Kunstmaan\FormBundle\Helper\FormMailer;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class FormMailerTest extends TestCase
{
    /**
     * @group legacy
     * @expectedDeprecation Passing the "@templating" service as the 2nd argument is deprecated since KunstmaanFormBundle 5.4 and will be replaced by the Twig service in KunstmaanFormBundle 6.0. Injected the "@twig" service instead.
     */
    public function testDeprecatedTemplating()
    {
        $mailer = $this->createMock(\Swift_Mailer::class);
        $templating = $this->createMock(EngineInterface::class);
        $container = $this->createMock(ContainerInterface::class);

        new FormMailer($mailer, $templating, $container);
    }

    public function testSendContactMail()
    {
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
     * @expectedDeprecation Passing the container as the 3th argument is deprecated since KunstmaanFormBundle 5.4 and will be replaced by the "request_stack" service in KunstmaanFormBundle 6.0. Injected the "@request_stack" service instead.
     */
    public function testDeprecatedConstructor()
    {
        $mailer = $this->createMock(\Swift_Mailer::class);
        $twig = $this->createMock(Environment::class);
        $requestStack = $this->createMock(RequestStack::class);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('get')->willReturn($requestStack);

        new FormMailer($mailer, $twig, $container);
    }
}
