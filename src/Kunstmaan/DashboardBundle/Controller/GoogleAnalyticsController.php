<?php
namespace Kunstmaan\DashboardBundle\Controller;

use Kunstmaan\DashboardBundle\Command\GoogleAnalyticsCommand;
use Kunstmaan\DashboardBundle\Entity\AnalyticsGoal;
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
        $em = $this->getDoctrine()->getManager();

        // get API client
        try {
            $this->container->get('kunstmaan_dashboard.googleclienthelper');
        } catch (\Exception $e) {
            // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
            $currentRoute = $request->attributes->get('_route');
            $currentUrl = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'setToken/';
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

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
            // get API client
            try {
                $googleClientHelper = $this->container->get('kunstmaan_dashboard.googleclienthelper');
            } catch (\Exception $e) {
                // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
                $currentRoute = $request->attributes->get('_route');
                $currentUrl = $this->get('router')->generate($currentRoute, array(), true);
                $params['url'] = $currentUrl . 'analytics/setToken/';

                return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
            }
            $googleClientHelper->getClient()->authenticate($code);
            $googleClientHelper->saveToken($googleClientHelper->getClient()->getAccessToken());

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
        // get API client
        try {
            $googleClientHelper = $this->container->get('kunstmaan_dashboard.googleclienthelper');
        } catch (\Exception $e) {
            // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
            $currentRoute = $request->attributes->get('_route');
            $currentUrl = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'analytics/setToken/';
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        if (null !== $request->request->get('accounts')) {
            $googleClientHelper->saveAccountId($request->request->get('accounts'));
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_PropertySelection'));
        }

        $analyticsHelper = $this->container->get('kunstmaan_dashboard.googleanalyticshelper');
        $analyticsHelper->init($googleClientHelper);
        $accounts = $analyticsHelper->getAccounts();

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
        // get API client
        try {
            $googleClientHelper = $this->container->get('kunstmaan_dashboard.googleclienthelper');
        } catch (\Exception $e) {
            // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
            $currentRoute = $request->attributes->get('_route');
            $currentUrl = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'analytics/setToken/';
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        if (null !== $request->request->get('properties')) {
            $googleClientHelper->savePropertyId($request->request->get('properties'));
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
        }

        $analyticsHelper = $this->container->get('kunstmaan_dashboard.googleanalyticshelper');
        $analyticsHelper->init($googleClientHelper);
        $properties = $analyticsHelper->getProperties();

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
        // get API client
        try {
            $googleClientHelper = $this->container->get('kunstmaan_dashboard.googleclienthelper');
        } catch (\Exception $e) {
            // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
            $currentRoute = $request->attributes->get('_route');
            $currentUrl = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'analytics/setToken/';
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        if (null !== $request->request->get('profiles')) {
            $googleClientHelper->saveProfileId($request->request->get('profiles'));
            return $this->redirect($this->generateUrl('kunstmaan_dashboard'));
        }

        $analyticsHelper = $this->container->get('kunstmaan_dashboard.googleanalyticshelper');
        $analyticsHelper->init($googleClientHelper);
        $profiles = $analyticsHelper->getProfiles();

        return $this->render(
            'KunstmaanDashboardBundle:GoogleAnalytics:profileSelection.html.twig',
            array('profiles' => $profiles)
        );
    }

    /**
     * Return an ajax response with all data for an overview
     *
     * @Route("/getOverview/{id}", requirements={"id" = "\d+"}, name="KunstmaanDashboardBundle_analytics_overview_ajax")
     *
     */
    public function getOverviewAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var AnalyticsOverviewRepository $analyticsOverviewRepository */
        $analyticsOverviewRepository = $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview');
        $overview = $analyticsOverviewRepository->getOverview($id);

        // goals data
        $goals = array();
        foreach ($overview->getActiveGoals() as $key => $goal) {
            /** @var AnalyticsGoal $goal */
            $goals[$key]['name'] = $goal->getName();
            $goals[$key]['visits'] = $goal->getVisits();
            $goals[$key]['id'] = $goal->getId();
            $goals[$key]['chartData'] = json_decode($goal->getChartData());
        }

        // overview data
        $overviewData = array(
            'id' => $overview->getId(),
            'chartData' => json_decode($overview->getChartData(), true),
            'chartDataMaxValue' => $overview->getChartDataMaxValue(),
            'title' => $overview->getTitle(),
            'timespan' => $overview->getTimespan(),
            'startOffset' => $overview->getStartOffset(),
            'sessions' => number_format($overview->getSessions()),
            'users' => number_format($overview->getUsers()),
            'pagesPerSession' => round($overview->getPagesPerSession(), 2),
            'avgSessionDuration' => $overview->getAvgSessionDuration(),
            'returningUsers' => number_format($overview->getReturningUsers()),
            'newUsers' => round($overview->getNewUsers(), 2),
            'pageViews' => number_format($overview->getPageViews()),
            'returningUsersPercentage' => $overview->getReturningUsersPercentage(),
            'newUsersPercentage' => $overview->getNewUsersPercentage(),
        );

        // put all data in the return array
        $return = array(
            'responseCode' => 200,
            'overview' => $overviewData,
            'goals' => $goals,
        );

        // return json response
        return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
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
     * @Route("/updateData", name="KunstmaanDashboardBundle_analytics_update")
     */
    public function runUpdate()
    {
        $command = new GoogleAnalyticsCommand();
        $command->setContainer($this->container);
        $input = new ArrayInput(array());
        $output = new NullOutput();
        $command->run($input, $output);

        return new JsonResponse(array(), 200, array('Content-Type' => 'application/json'));
    }
}
