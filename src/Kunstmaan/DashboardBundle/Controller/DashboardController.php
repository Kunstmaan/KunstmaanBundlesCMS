<?php

namespace Kunstmaan\DashboardBundle\Controller;

use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DashboardController extends Controller
{

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="kunstmaan_dashboard")
     * @Template()
     *
     * @return array
     */
    public function indexAction()
    {
        /** @var WidgetManager $widgetManager */
        $widgetManager = $this->get('kunstmaan_dashboard.manager.widgets');
        /** @var DashboardWidget[] $widgets */
        $widgets = $widgetManager->getWidgets();
        return $this->render('KunstmaanDashboardBundle:Dashboard:index.html.twig', array('widgets' => $widgets));
    }
}
