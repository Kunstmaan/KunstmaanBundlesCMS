<?php

namespace Kunstmaan\RedirectBundle\Form\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormEvents;

final class RedirectPathNormalizerFormEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'normalizeRedirectPath',
        ];
    }

    public function normalizeRedirectPath(PreSubmitEvent $event): void
    {
        $path = $event->getData();
        if (!$path) {
            return;
        }

        if (null !== parse_url($path, \PHP_URL_SCHEME)) {
            return;
        }

        $event->setData('/' . ltrim(trim($path), '/'));
    }
}
