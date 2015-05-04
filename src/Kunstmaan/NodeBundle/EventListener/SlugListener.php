<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;


/**
 * Class SlugListener
 * @package Kunstmaan\NodeBundle\EventListener
 */
class SlugListener
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var ControllerResolver
     */
    protected $resolver;


    /**
     * @var EventDispatcher
     */
    protected $eventDispatcher;

    /**
     * @param EntityManager $entityManager
     * @param ControllerResolver $resolver
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManager $entityManager, ControllerResolver $resolver, EventDispatcherInterface $eventDispatcher)
    {
        $this->em = $entityManager;
        $this->resolver = $resolver;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();

        //check if the event has anything to do with nodeTranslations, if not this method can be skipped
        if (!$request->attributes->has('_nodeTranslation')) {
            return;
        }

        $nodeTranslation = $request->attributes->get('_nodeTranslation');
        $entity = $nodeTranslation->getRef($this->em);

        //if the entity is an instance of the SlugControllerActionInterface, change the controller
        if ($entity instanceof SlugActionInterface) {
            $request->attributes->set('_entity', $entity);

            //do security check by firing an event that gets handled by the SlugSecurityListener
            $securityEvent = new SlugSecurityEvent();
            $this->eventDispatcher->dispatch(Events::SLUG_SECURITY, $securityEvent);

           //set the right controller
            $request->attributes->set('_controller', $entity->getControllerAction());
            $event->setController($this->resolver->getController($request));
        }
    }
}