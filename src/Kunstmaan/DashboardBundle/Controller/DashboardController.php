<?php

namespace Kunstmaan\DashboardBundle\Controller;

use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends Controller
{
    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="kunstmaan_dashboard")
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var WidgetManager $widgetManager */
        $widgetManager = $this->get('kunstmaan_dashboard.manager.widgets');
        /** @var DashboardWidget[] $widgets */
        $widgets = $widgetManager->getWidgets();
        $segmentId = $request->query->get('segment');

        return $this->render('@KunstmaanDashboard/Dashboard/index.html.twig', ['widgets' => $widgets, 'id' => $segmentId]);
    }
}
