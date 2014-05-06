<?php
namespace Kunstmaan\DashboardBundle\Controller;

use Kunstmaan\DashboardBundle\Command\GoogleAnalyticsCommand;
use Kunstmaan\DashboardBundle\Entity\AnalyticsGoal;
use Kunstmaan\DashboardBundle\Helper\GoogleClientHelper;
use Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository;
use Kunstmaan\DashboardBundle\Repository\AnalyticsOverviewRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class GoogleAnalyticsController extends Controller
{

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="KunstmaanDashboardBundle_widget_googleanalytics")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return array
     */
    public function widgetAction(Request $request)
    {
        $params['redirect_uri'] = $this->get('router')->generate('KunstmaanDashboardBundle_setToken', array(), true);
        $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

        // if token not set
        if (!$configHelper->tokenIsSet()) {
            $params['authUrl'] = $configHelper->getAuthUrl($params['redirect_uri']);
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        // if propertyId not set
        if (!$configHelper->accountIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_AccountSelection'));
        }

        // if propertyId not set
        if (!$configHelper->propertyIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_PropertySelection'));
        }

        // if profileId not set
        if (!$configHelper->profileIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
        }

        $em = $this->getDoctrine()->getManager();

        // set the overviews param
        $params['token'] = true;
        $params['overviews'] = $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getAll();
        /** @var AnalyticsConfigRepository $analyticsConfigRepository */
        $analyticsConfigRepository = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $date = $analyticsConfigRepository->getConfig()->getLastUpdate();
        if ($date) {
            $params['last_update'] = $date->format('d-m-Y H:i');
        } else {
            $params['last_update'] = "never";
        }
        return $params;
    }


    /**
     * @Route("/setToken/", name="KunstmaanDashboardBundle_setToken")
     *
     * @param Request $request
     *
     * @return array
     */
    public function setTokenAction(Request $request)
    {
        $code = $request->query->get('code');

        if (isset($code)) {
            $clientHelper = $this->container->get('kunstmaan_dashboard.helper.google.client');
            $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

            $clientHelper->getClient()->authenticate($code);
            $configHelper->saveToken($clientHelper->getClient()->getAccessToken());

            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_AccountSelection'));
        }

        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_widget_googleanalytics'));
    }


    /**
     * @Route("/selectAccount", name="KunstmaanDashboardBundle_AccountSelection")
     *
     * @param Request $request
     *
     * @return array
     */
    public function accountSelectionAction(Request $request)
    {
        $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

        if (null !== $request->request->get('accounts')) {
            $configHelper->saveAccountId($request->request->get('accounts'));
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_PropertySelection'));
        }

        $accounts = $configHelper->getAccounts();

        return $this->render(
            'KunstmaanDashboardBundle:GoogleAnalytics:accountSelection.html.twig',
            array('accounts' => $accounts)
        );
    }

    /**
     * @Route("/selectWebsite", name="KunstmaanDashboardBundle_PropertySelection")
     *
     * @param Request $request
     *
     * @return array
     */
    public function propertySelectionAction(Request $request)
    {
        $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

        if (null !== $request->request->get('properties')) {
            $parts = explode('::', $request->request->get('properties'));
            $configHelper->savePropertyId($parts[0]);
            $configHelper->saveConfigName($parts[1]);
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
        }

        $properties = $configHelper->getProperties();

        return $this->render(
            'KunstmaanDashboardBundle:GoogleAnalytics:propertySelection.html.twig',
            array('properties' => $properties)
        );
    }

    /**
     * @Route("/selectProfile", name="KunstmaanDashboardBundle_ProfileSelection")
     *
     * @param Request $request
     *
     * @return array
     */
    public function profileSelectionAction(Request $request)
    {
        $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

        if (null !== $request->request->get('profiles')) {
            $configHelper->saveProfileId($request->request->get('profiles'));
            return $this->redirect($this->generateUrl('kunstmaan_dashboard'));
        }

        $profiles = $configHelper->getProfiles();

        return $this->render(
            'KunstmaanDashboardBundle:GoogleAnalytics:profileSelection.html.twig',
            array('profiles' => $profiles)
        );
    }

    /**
     * @Route("/resetProfile", name="KunstmaanDashboardBundle_analytics_resetProfile")
     */
    public function resetProfileAction()
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->resetProfileId();
        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
    }

    /**
     * @Route("/resetProperty", name="KunstmaanDashboardBundle_analytics_resetProperty")
     */
    public function resetPropertyAction()
    {
        $em = $this->getDoctrine()->getManager();
        $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->resetPropertyId();
        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_AccountSelection'));
    }

    /**
     * @Route("/test", name="KunstmaanDashboardBundle_test")
     */
    public function testAction(Request $request) {



        $query = $this->container->get('kunstmaan_dashboard.helper.google.analytics.query');

        $extra = array(

            );
        $results = $query->getResults(
                14,1,
                'ga:sessions, ga:users, ga:pageviews, ga:percentNewSessions',
                $extra
            );
        $rows = $results->getRows();

        var_dump(
                $rows
            );


        exit;

        $googleClientHelper = $this->container->get('kunstmaan_dashboard.googleclienthelper');
        $analyticsHelper = $this->container->get('kunstmaan_dashboard.googleanalyticshelper');
        $analyticsHelper->init($googleClientHelper);


        $extra = array(

            );

        $results = $analyticsHelper->getResults(
                14,1,
                'ga:sessions, ga:users, ga:pageviews, ga:percentNewSessions',
                $extra
            );
        $rows = $results->getRows();

        var_dump(
                $rows
            );

        exit;
    }
}
