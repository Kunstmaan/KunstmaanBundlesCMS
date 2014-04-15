<?php
namespace Kunstmaan\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GoogleAnalyticsController extends Controller
{

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="kunstmaan_dashboard_widget_googleanalytics")
     * @Template()
     *
     * @return array
     */
    public function widgetAction()
    {
        return array('name' => "GA! woooo!");
    }

}