<?php

namespace Kunstmaan\SeoBundle\Controller;

use Kunstmaan\SeoBundle\Event\RobotsEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class RobotsController extends AbstractController
{
    private $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route(path="/robots.txt", name="KunstmaanSeoBundle_robots", defaults={"_format": "txt"})
     * @Template(template="@KunstmaanSeo/Admin/Robots/index.html.twig")
     *
     * @return array
     */
    public function __invoke(Request $request)
    {
        $event = new RobotsEvent();

        $event = $this->dispatcher->dispatch($event);

        return ['robots' => $event->getContent()];
    }
}
