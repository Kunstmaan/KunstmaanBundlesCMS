<?php

namespace Kunstmaan\NodeBundle\EventListener;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Templating\EngineInterface;

/**
 * Class RenderContextListener
 * @package Kunstmaan\NodeBundle\EventListener
 */
class RenderContextListener
{




    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @param EntityManager $em
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating)
    {
        $this->templating = $templating;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request    = $event->getRequest();
        $nodeTranslation = $request->attributes->get('_nodeTranslation');

        if($nodeTranslation) {
        //fetch parameters;
            $entity          = $request->attributes->get('_entity');
            $url             = $request->attributes->get('url');
            $nodeMenu        = $request->attributes->get('_nodeMenu');
            $parameters      = $request->attributes->get('_renderContext');
            $renderContext = array(
                '_nodeTranslation' => $nodeTranslation,
                'slug' => $url,
                'page' => $entity,
                'resource' => $entity,
                'nodemenu' => $nodeMenu,
                );

            $parameters = array_merge($renderContext, $parameters);
            //sent the response here, another option is to let the symfony kernel.view listener handle it
            $event->setResponse($this->templating->renderResponse($entity->getDefaultView(), $parameters));
        }
    }
}