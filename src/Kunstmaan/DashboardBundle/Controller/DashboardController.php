<?php

namespace Kunstmaan\DashboardBundle\Controller;

use Kunstmaan\DashboardBundle\Helper\GoogleClientHelper;
use Kunstmaan\DashboardBundle\Manager\WidgetManager;
use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DashboardController extends Controller
{

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="kunstmaan_dashboard")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        $params['redirect_uri'] = $this->get('router')->generate('KunstmaanDashboardBundle_setToken', array(), true);

        // get API client
        try {
            /** @var GoogleClientHelper $googleClientHelper */
            $googleClientHelper = $this->container->get('kunstmaan_dashboard.googleclienthelper');
        } catch (\Exception $e) {
            // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
            $currentRoute = $request->attributes->get('_route');
            $currentUrl = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'setToken/';

            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        // if token not set
        if (!$googleClientHelper->tokenIsSet()) {
            $currentRoute = $request->attributes->get('_route');
            $currentUrl = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'setToken/';


            $googleClient = $googleClientHelper->getClient();
            $params['authUrl'] = $googleClient->createAuthUrl();
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        // if propertyId not set
        if (!$googleClientHelper->accountIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_AccountSelection'));
        }

        // if propertyId not set
        if (!$googleClientHelper->propertyIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_PropertySelection'));
        }

        // if profileId not set
        if (!$googleClientHelper->profileIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
        }


        // if setup completed
        /** @var WidgetManager $widgetManager */
        $widgetManager = $this->get('kunstmaan_dashboard.manager.widgets');
        /** @var DashboardWidget[] $widgets */
        $widgets = $widgetManager->getWidgets();
        return $this->render('KunstmaanDashboardBundle:Dashboard:index.html.twig', array('widgets' => $widgets));
    }
}
