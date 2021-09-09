<?php

declare(strict_types=1);

namespace Kunstmaan\SeoBundle\EventListener;

use Kunstmaan\SeoBundle\Event\RobotsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class ParameterRobotsTxtListener implements EventSubscriberInterface
{
    private $fallback;

    public function __construct(string $fallback)
    {
        $this->fallback = $fallback;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RobotsEvent::class => ['__invoke', -100],
        ];
    }

    public function __invoke(RobotsEvent $event): void
    {
        if (empty($event->getContent())) {
            $event->setContent($this->fallback);
        }
    }
}
