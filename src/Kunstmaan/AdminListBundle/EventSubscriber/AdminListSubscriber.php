<?php

namespace Kunstmaan\AdminListBundle\EventSubscriber;

use Kunstmaan\AdminListBundle\Entity\OverviewNavigationInterface;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class AdminListSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_DELETE => 'postDelete',
        ];
    }

    public function postDelete(NodeEvent $event)
    {
        $page = $event->getPage();

        // Redirect to admin list when deleting a page that implements the OverviewNavigationInterface.
        if ($page instanceof OverviewNavigationInterface) {
            $route = $this->router->generate($page->getOverViewRoute());
            $response = new RedirectResponse($route);

            $event->setResponse($response);
        }
    }
}
