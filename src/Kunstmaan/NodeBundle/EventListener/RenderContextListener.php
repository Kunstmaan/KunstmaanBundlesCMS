<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Templating\EngineInterface;

class RenderContextListener
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    protected $em;

    /**
     * @param EngineInterface $templating
     */
    public function __construct(EngineInterface $templating, EntityManager $em)
    {
        $this->templating = $templating;
        $this->em = $em;
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

            if ($request->get('preview') == true) {
                $version = $request->get('version');
                if (!empty($version) && is_numeric($version)) {
                    $nodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->find($version);
                    if (!is_null($nodeVersion)) {
                        $entity = $nodeVersion->getRef($this->em);
                    }
                }
            }

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
