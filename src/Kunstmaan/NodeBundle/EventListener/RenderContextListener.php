<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Templating\EngineInterface;

class RenderContextListener
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
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

        if ($nodeTranslation) {
            $entity          = $request->attributes->get('_entity');
            $url             = $request->attributes->get('url');
            $nodeMenu        = $request->attributes->get('_nodeMenu');
            $parameters      = $request->attributes->get('_renderContext');

            $renderContext = array(
                'nodetranslation'   => $nodeTranslation,
                'slug'              => $url,
                'page'              => $entity,
                'resource'          => $entity,
                'nodemenu'          => $nodeMenu,
            );

            if (is_array($parameters) || $parameters instanceof \ArrayObject) {
                $parameters = array_merge($renderContext, (array)$parameters);
            } else {
                $parameters = $renderContext;
            }

            // Sent the response here, another option is to let the symfony kernel.view listener handle it
            $event->setResponse($this->templating->renderResponse($entity->getDefaultView(), $parameters));
        }
    }
}
