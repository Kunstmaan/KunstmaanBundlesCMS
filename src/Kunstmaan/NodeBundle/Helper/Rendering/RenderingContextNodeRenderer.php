<?php

namespace Kunstmaan\NodeBundle\Helper\Rendering;

use Kunstmaan\NodeBundle\Controller\SlugController;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\SlugEvent;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RenderingContextNodeRenderer implements NodeRenderingInterface
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var NodeMenu */
    private $nodeMenu;

    public function __construct(EventDispatcherInterface $eventDispatcher, NodeMenu $nodeMenu)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->nodeMenu = $nodeMenu;
    }

    public function render(string $locale, Node $node, NodeTranslation $nodeTranslation, HasNodeInterface $page, Request $request)
    {
        $nodeMenu = $this->nodeMenu;
        $nodeMenu->setLocale($locale);
        $nodeMenu->setCurrentNode($node);
        $nodeMenu->setIncludeOffline(false);

        $renderContext = new RenderContext(
            [
                'nodetranslation' => $nodeTranslation,
                'slug' => $nodeTranslation->getSlug(),
                'page' => $page,
                'resource' => $page,
                'nodemenu' => $nodeMenu,
            ]
        );
        if (method_exists($page, 'getDefaultView')) {
            $renderContext->setView($page->getDefaultView());
        }
        $preEvent = new SlugEvent(null, $renderContext);
        $this->dispatch($preEvent, Events::PRE_SLUG_ACTION);
        $renderContext = $preEvent->getRenderContext();

        $postEvent = new SlugEvent(null, $renderContext);
        $this->dispatch($postEvent, Events::POST_SLUG_ACTION);

        $response = $postEvent->getResponse();
        $renderContext = $postEvent->getRenderContext();

        if ($response instanceof Response) {
            return $response;
        }

        $view = $renderContext->getView();
        if (empty($view)) {
            throw $this->createNotFoundException(sprintf('Missing view path for page "%s"', \get_class($page)));
        }

        $template = new Template([]);
        $template->setTemplate($view);
        $template->setOwner([SlugController::class, 'slugAction']);

        $request->attributes->set('_template', $template);

        return $renderContext->getArrayCopy();
    }

    private function dispatch($event, string $eventName)
    {
        $eventDispatcher = $this->eventDispatcher;
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);

            return $eventDispatcher->dispatch($event, $eventName);
        }

        return $eventDispatcher->dispatch($eventName, $event);
    }
}
