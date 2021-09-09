<?php

declare(strict_types=1);

namespace Kunstmaan\SeoBundle\EventListener;

use Kunstmaan\SeoBundle\Event\RobotsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class FileRobotsTxtListener implements EventSubscriberInterface
{
    private $path;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RobotsEvent::class => ['__invoke', 0],
        ];
    }

    public function __invoke(RobotsEvent $event): void
    {
        if (empty($event->getContent()) && file_exists($this->path)) {
            $event->setContent(file_get_contents($this->path));
        }
    }
}
