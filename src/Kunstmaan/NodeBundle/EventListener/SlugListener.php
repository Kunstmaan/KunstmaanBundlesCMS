<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolverInterface;
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

    /**
     * SlugListener constructor.
     *
     * @param EntityManager               $em
     * @param ControllerResolverInterface $resolver
     * @param EventDispatcherInterface    $eventDispatcher
     */
    public function __construct(EntityManager $em, ControllerResolverInterface $resolver, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $em;
        $this->resolver = $resolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param FilterControllerEvent $event
     *
     * @throws \Exception
     */
    public function onKernelController(FilterControllerEvent $event)
    {
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

            $this->eventDispatcher->dispatch(Events::SLUG_SECURITY, $securityEvent);

            // Set the right controller
            $request->attributes->set('_controller', $entity->getControllerAction());
            $event->setController($this->resolver->getController($request));
        }
    }
}
