<?php

namespace Kunstmaan\DashboardBundle\Controller;

use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DashboardController extends AbstractController
{
    /** @var WidgetManager */
    private $widgetManager;

    public function __construct(WidgetManager $widgetManager)
    {
        $this->widgetManager = $widgetManager;
    }

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @return Response
     */
    #[Route(path: '/', name: 'kunstmaan_dashboard')]
    public function indexAction(Request $request)
    {
        $widgets = $this->widgetManager->getWidgets();
        $segmentId = $request->query->get('segment');

        return $this->render('@KunstmaanDashboard/Dashboard/index.html.twig', ['widgets' => $widgets, 'id' => $segmentId]);
    }
}
