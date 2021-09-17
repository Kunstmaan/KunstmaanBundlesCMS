<?php

namespace Symfony\Component\Mailer;

use Symfony\Component\Mime\RawMessage;

//NEXT_MAJOR: when sf 3.4 support is dropped this stub can also be removed
interface MailerInterface
{
    public function send(RawMessage $message, Envelope $envelope = null): void;
}

class Envelope {

}
