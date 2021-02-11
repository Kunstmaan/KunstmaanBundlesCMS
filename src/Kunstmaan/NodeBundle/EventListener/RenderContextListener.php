<?php

namespace Kunstmaan\NodeBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
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
    public function __construct(/* EngineInterface|EntityManagerInterface */ $em, EntityManagerInterface $emOld = null)
    {
        if ($em instanceof EngineInterface) {
            // NEXT_MAJOR Also remove the symfony/templating dependency as it is unused after the removal of the templating parameter.
            @trigger_error(sprintf('Passing a templating engine as the first argument of "%s" is deprecated since KunstmaanNodeBundle 5.1 and will be removed in KunstmaanNodeBundle 6.0. Remove the template engine service argument.', __METHOD__), E_USER_DEPRECATED);

            $this->templating = $em;
            $this->em = $emOld;

            return;
        }

        $this->em = $em;
    }

    /**
     * @param GetResponseForControllerResultEvent|ViewEvent $event
     */
    public function onKernelView($event)
    {
        if (!$event instanceof GetResponseForControllerResultEvent && !$event instanceof ViewEvent) {
            throw new \InvalidArgumentException(\sprintf('Expected instance of type %s, %s given', \class_exists(RequestEvent::class) ? ViewEvent::class : GetResponseForControllerResultEvent::class, \is_object($event) ? \get_class($event) : \gettype($event)));
        }

        $response = $event->getControllerResult();
        if ($response instanceof Response) {
            // If it's a response, just continue
            return;
        }

        $request = $event->getRequest();
        if ($request->attributes->has('_template')) { //template is already set
            return;
        }

        $nodeTranslation = $request->attributes->get('_nodeTranslation');
        if ($nodeTranslation) {
            $entity = $request->attributes->get('_entity');
            $url = $request->attributes->get('url');
            $nodeMenu = $request->attributes->get('_nodeMenu');
            $parameters = $request->attributes->get('_renderContext');

            if ($request->get('preview') === true) {
                $version = $request->get('version');
                if (!empty($version) && is_numeric($version)) {
                    $nodeVersion = $this->em->getRepository(NodeVersion::class)->find($version);
                    if (!\is_null($nodeVersion)) {
                        $entity = $nodeVersion->getRef($this->em);
                    }
                }
            }

            $renderContext = [
                'nodetranslation' => $nodeTranslation,
                'slug' => $url,
                'page' => $entity,
                'resource' => $entity,
                'nodemenu' => $nodeMenu,
            ];

            if (\is_array($parameters) || $parameters instanceof \ArrayObject) {
                $parameters = array_merge($renderContext, (array) $parameters);
            } else {
                $parameters = $renderContext;
            }

            if (\is_array($response)) {
                // If the response is an array, merge with rendercontext
                $parameters = array_merge($parameters, $response);
            }

            //set the rendercontext with all params as response, plus the template in the request attribs
            //the SensioFrameworkExtraBundle kernel.view will handle everything else
            $event->setControllerResult((array) $parameters);

            $template = new Template([]);
            $template->setTemplate($entity->getDefaultView());

            $controllerInfo = $this->getControllerInfo($request->attributes->get('_controller'));
            $template->setOwner($controllerInfo);

            $request->attributes->set('_template', $template);
        }
    }

    /**
     * BC check to return correct controller/action information.
     *
     * @param string $controllerString
     *
     * @return array
     */
    private function getControllerInfo($controllerString)
    {
        if (strpos($controllerString, '::') !== false) {
            $controllerBits = explode('::', $controllerString);
            $action = array_pop($controllerBits);

            return [$controllerBits, $action];
        }

        // NEXT_MAJOR: Remove BC check when we drop sf 3.4 support
        $controllerBits = explode(':', $controllerString);
        $action = array_pop($controllerBits);

        return [implode(':', $controllerBits), $action];
    }
}
