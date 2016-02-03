<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Templating\EngineInterface;

class RenderContextListener
{
    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @param EngineInterface        $templating
     * @param EntityManagerInterface $em
     */
    public function __construct(EngineInterface $templating, EntityManagerInterface $em)
    {
        $this->templating = $templating;
        $this->em         = $em;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $response = $event->getControllerResult();
        if ($response instanceof Response) {
            // If it's a response, just continue
            return;
        }

        $request = $event->getRequest();
        if ($request->attributes->has('_template')) { //template is already set
            return;
        }

        $nodeTranslation    = $request->attributes->get('_nodeTranslation');
        if ($nodeTranslation) {
            $entity     = $request->attributes->get('_entity');
            $url        = $request->attributes->get('url');
            $nodeMenu   = $request->attributes->get('_nodeMenu');
            $parameters = $request->attributes->get('_renderContext');

            if ($request->get('preview') === true) {
                $version = $request->get('version');
                if (!empty($version) && is_numeric($version)) {
                    $nodeVersion = $this->em->getRepository('KunstmaanNodeBundle:NodeVersion')->find($version);
                    if (!is_null($nodeVersion)) {
                        $entity = $nodeVersion->getRef($this->em);
                    }
                }
            }

            $renderContext = array(
                'nodetranslation' => $nodeTranslation,
                'slug'            => $url,
                'page'            => $entity,
                'resource'        => $entity,
                'nodemenu'        => $nodeMenu,
            );

            if (is_array($parameters) || $parameters instanceof \ArrayObject) {
                $parameters = array_merge($renderContext, (array) $parameters);
            } else {
                $parameters = $renderContext;
            }

            if (is_array($response)) {
                // If the response is an array, merge with rendercontext
                $parameters = array_merge($parameters, $response);
            }

            //set the rendercontext with all params as response, plus the template in the request attribs
            //the SensioFrameworkExtraBundle kernel.view will handle everything else
            $event->setControllerResult((array) $parameters);
            $request->attributes->set('_template', $entity->getDefaultView());
        }
    }
}
