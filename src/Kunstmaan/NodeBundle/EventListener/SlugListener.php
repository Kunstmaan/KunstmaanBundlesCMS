<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\AdminRouteHelper;
use Kunstmaan\NodeBundle\Controller\SlugActionInterface;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\SlugSecurityEvent;
use Kunstmaan\NodeBundle\Repository\NodeVersionRepository;
use Kunstmaan\NodeBundle\Router\SlugRouter;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
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
    public function __construct(
        EntityManager $em,
        ControllerResolverInterface $resolver,
        EventDispatcherInterface $eventDispatcher
    ) {
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

        $entity = $this->getEntity($nodeTranslation, $request);

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

    /**
     * @param NodeTranslation $nodeTranslation
     * @param Request         $request
     *
     * @return null|object
     */
    private function getEntity(NodeTranslation $nodeTranslation, Request $request)
    {
        $versionId = $request->query->get('version');

        if ($request->attributes->get('_route') !== SlugRouter::$SLUG_PREVIEW || $versionId === null) {
            return $nodeTranslation->getRef($this->em);
        }

        /** @var NodeVersionRepository $nodeVersionRepository */
        $nodeVersionRepository = $this->em->getRepository(NodeVersion::class);

        $nodeVersion = $nodeVersionRepository->findOneBy([
            'nodeTranslation' => $nodeTranslation,
            'id' => $versionId
        ]);

        if (!$nodeVersion instanceof NodeVersion) {
            return null;
        }

        return $nodeTranslation->getRefByNodeVersion($this->em, $nodeVersion);
    }
}
