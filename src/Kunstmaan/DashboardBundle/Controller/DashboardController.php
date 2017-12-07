<?php

namespace Kunstmaan\DashboardBundle\Controller;

use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends AbstractController
{
    /**
     * @var WidgetManager
     */
    protected $widgetManager;

    /**
     * @required
     * @param WidgetManager $widgetManager
     */
    public function setWidgetManager(WidgetManager $widgetManager)
    {
        $this->widgetManager = $widgetManager;
    }

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="kunstmaan_dashboard")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function indexAction(Request $request, $segmentId=null)
    {
        /** @var DashboardWidget[] $widgets */
        $widgets = $this->widgetManager->getWidgets();
        $segmentId = $request->query->get('segment');
        return $this->render('KunstmaanDashboardBundle:Dashboard:index.html.twig', array('widgets' => $widgets, 'id' => $segmentId));
    }
}
