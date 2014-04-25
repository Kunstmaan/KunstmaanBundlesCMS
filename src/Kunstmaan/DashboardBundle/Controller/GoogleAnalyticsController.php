<?php
namespace Kunstmaan\DashboardBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

use Kunstmaan\DashboardBundle\Command\GoogleAnalyticsCommand;

class GoogleAnalyticsController extends Controller
{

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="KunstmaanDashboardBundle_widget_googleanalytics")
     * @Template()
     *
     * @return array
     */
    public function widgetAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // get API client
        try {
            $googleClientHelper = $this->container->get('kunstmaan_dashboard.googleclienthelper');
        } catch (\Exception $e) {
            // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
            $currentRoute  = $request->attributes->get('_route');
            $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'setToken/';
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        // set the overviews param
        $params['token'] = true;
        $params['overviews'] = $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getAll();
        $date = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->getconfig()->getLastUpdate();
        if ($date) {
          $params['last_update'] = $date->format('d-m-Y H:i');
        } else {
          $params['last_update'] = '/';
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
                $currentRoute  = $request->attributes->get('_route');
                $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
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
            $currentRoute  = $request->attributes->get('_route');
            $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'analytics/setToken/';
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        if (null !== $request->request->get('accounts')) {
            $googleClientHelper->saveAccountId($request->request->get('accounts'));
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_PropertySelection'));
        }

        /** @var GoogleClientHelper $googleClient */
        $googleClient    = $googleClientHelper->getClient();
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
            $currentRoute  = $request->attributes->get('_route');
            $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'analytics/setToken/';
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        if (null !== $request->request->get('properties')) {
            $googleClientHelper->savePropertyId($request->request->get('properties'));
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
        }

        /** @var GoogleClientHelper $googleClient */
        $googleClient    = $googleClientHelper->getClient();
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
            $currentRoute  = $request->attributes->get('_route');
            $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'analytics/setToken/';
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        if (null !== $request->request->get('profiles')) {
            $parts = explode('::', $request->request->get('profiles'))
            $googleClientHelper->saveProfileId($parts[0]);
            $googleClientHelper->saveConfigName($parts[1]);
            return $this->redirect($this->generateUrl('kunstmaan_dashboard'));
        }

        /** @var GoogleClientHelper $googleClient */
        $googleClient    = $googleClientHelper->getClient();
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
        if (!$id) {
            $return = array(
              'responseCode'                        => 400
            );
        }

        $em       = $this->getDoctrine()->getManager();
        $overview = $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getOverview($id);

        // goals data
        $goals = array();
        foreach ($overview->getActiveGoals() as $key => $goal) {
            $goals[$key]['name']       = $goal->getName();
            $goals[$key]['visits']     = $goal->getVisits();
            $goals[$key]['id']         = $goal->getId();
            $goals[$key]['chartData'] = json_decode($goal->getChartData());
        }

        // overview data
        $overviewData = array(
          'id'                                  => $overview->getId(),
          'chartData'                           => json_decode($overview->getChartData(), true),
          'chartDataMaxValue'                   => $overview->getChartDataMaxValue(),
          'title'                               => $overview->getTitle(),
          'timespan'                            => $overview->getTimespan(),
          'startOffset'                         => $overview->getStartOffset(),
          'sessions'                            => number_format($overview->getSessions()),
          'users'                               => number_format($overview->getUsers()),
          'pagesPerSession'                     => round($overview->getPagesPerSession(), 2),
          'avgSessionDuration'                  => $overview->getAvgSessionDuration(),
          'returningUsers'                      => number_format($overview->getReturningUsers()),
          'newUsers'                            => round($overview->getNewUsers(),2),
          'pageViews'                           => number_format($overview->getPageViews()),
          'returningUsersPercentage'            => $overview->getReturningUsersPercentage(),
          'newUsersPercentage'                  => $overview->getNewUsersPercentage(),
        );

        // put all data in the return array
        $return = array(
          'responseCode'                        => 200,
          'overview'                            => $overviewData,
          'goals'                               => $goals,
        );

        // return json response
        return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * @Route("/resetProfile", name="KunstmaanDashboardBundle_analytics_resetProfile")
     */
    public function resetProfileAction()
    {
        $em            = $this->getDoctrine()->getManager();
        $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->resetProfileId();
        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
    }

    /**
     * @Route("/resetProperty", name="KunstmaanDashboardBundle_analytics_resetProperty")
     */
    public function resetPropertyAction()
    {
        $em            = $this->getDoctrine()->getManager();
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
      $resultCode = $command->run($input, $output);

      return new JsonResponse(array(), 200, array('Content-Type' => 'application/json'));
    }


    /**
     * @Route("/test", name="KunstmaanDashboardBundle_homepage_test")
     * @Template()
     *
     * @return array
     */
    public function testAction()
    {
        $googleClientHelper = $this->container->get('kunstmaan_dashboard.googleclienthelper');
        $analyticsHelper = $this->container->get('kunstmaan_dashboard.googleanalyticshelper');
        $analyticsHelper->init($googleClientHelper);

        $results = $analyticsHelper->getResults(
            1,
            1,
            'ga:pageviewsPerSession'
        );


        var_dump($results->getRows());

        exit;
    }

}
