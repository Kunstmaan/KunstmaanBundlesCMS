<?php

declare(strict_types=1);

namespace Kunstmaan\SeoBundle\EventListener;

use Doctrine\Persistence\ObjectRepository;
use Kunstmaan\SeoBundle\Entity\Robots;
use Kunstmaan\SeoBundle\Event\RobotsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class AdminRobotsTxtListener implements EventSubscriberInterface
{
    private $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RobotsEvent::class => ['__invoke', 100],
        ];
    }

    public function __invoke(RobotsEvent $event): void
    {
        $entity = $this->repository->findOneBy([]);
        if (!$entity instanceof Robots) {
            return;
        }

        $content = $entity->getRobotsTxt();
        if ($content === null) {
            return;
        }

        $event->setContent($content);
    }
}
