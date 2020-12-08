<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class SlugListener
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ControllerResolverInterface
     */
    protected $resolver;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(EntityManager $em, ControllerResolverInterface $resolver, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->resolver = $resolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param FilterControllerEvent|ControllerEvent $event
     *
     * @throws \Exception
     */
    public function onKernelController($event)
    {
        if (!$event instanceof FilterControllerEvent && !$event instanceof ControllerEvent) {
            throw new \InvalidArgumentException(\sprintf('Expected instance of type %s, %s given', \class_exists(ControllerEvent::class) ? ControllerEvent::class : FilterControllerEvent::class, \is_object($event) ? \get_class($event) : \gettype($event)));
        }

        $request = $event->getRequest();

        // Check if the event has a nodeTranslation, if not this method can be skipped
        if (!$request->attributes->has('_nodeTranslation')) {
            return;
        }

        $nodeTranslation = $request->attributes->get('_nodeTranslation');
        if (!($nodeTranslation instanceof NodeTranslation)) {
            throw new \Exception('Invalid _nodeTranslation value found in request attributes');
        }
        $entity = $nodeTranslation->getRef($this->em);

        // If the entity is an instance of the SlugActionInterface, change the controller
        if ($entity instanceof SlugActionInterface) {
            $request->attributes->set('_entity', $entity);

            // Do security check by firing an event that gets handled by the SlugSecurityListener
            $securityEvent = new SlugSecurityEvent();
            $securityEvent
                ->setNode($nodeTranslation->getNode())
                ->setEntity($entity)
                ->setRequest($request)
                ->setNodeTranslation($nodeTranslation);

            $this->dispatch($securityEvent, Events::SLUG_SECURITY);

            // Set the right controller
            $request->attributes->set('_controller', $entity->getControllerAction());
            $event->setController($this->resolver->getController($request));
        }
    }

    /**
     * @param object $event
     *
     * @return object
     */
    private function dispatch($event, string $eventName)
    {
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($this->eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $this->eventDispatcher->dispatch($eventName, $event);
    }
}
