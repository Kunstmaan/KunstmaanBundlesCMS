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
        $params['redirect_uri'] = $this->get('router')->generate('KunstmaanDashboardBundle_setToken', array(), true);

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


        // if token not set
        if (!$googleClientHelper->tokenIsSet()) {
            $currentRoute  = $request->attributes->get('_route');
            $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'setToken/';

            $googleClient      = $googleClientHelper->getClient();
            $params['authUrl'] = $googleClient->createAuthUrl();
            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        // if propertyId not set
        if (!$googleClientHelper->propertyIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_PropertySelection'));
        }

        // if profileId not set
        if (!$googleClientHelper->profileIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
        }

        // if setup is complete
        $em        = $this->getDoctrine()->getManager();
        $overviews = $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getAll();

        $params['token']     = true;
        $params['overviews'] = array();

        // if no overviews are yet configured
        if (!$overviews) {
            return $this->render(
                'KunstmaanDashboardBundle:GoogleAnalytics:errorOverviews.html.twig',
                array()
            );
        }

        // set the overviews param
        $params['overviews'] = $overviews;
        // set the default overview
        $params['overview'] = $overviews[0];
        if (sizeof($overviews) >= 3) { // if all overviews are present
            // set the default overview to the middle one
            $params['overview'] = $overviews[2];
        }
        $params['referrals'] = $params['overview']->getReferrals()->toArray();
        $params['searches']  = $params['overview']->getSearches()->toArray();
        if (null !== $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->getConfig()->getLastUpdate()) {
            $timestamp = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->getConfig()->getLastUpdate()->getTimestamp ();
            $params['lastUpdate'] = date('H:i (d/m/Y)', $timestamp);
        } else {
            $params['lastUpdate'] = '/';
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

            $googleClientHelper->getClient()->authenticate();
            $googleClientHelper->saveToken($googleClientHelper->getClient()->getAccessToken());

            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_PropertySelection'));
        }

        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_homepage'));
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
            $parts = explode("::", $request->request->get('properties'));
            $googleClientHelper->saveAccountId($parts[1]);
            $googleClientHelper->savePropertyId($parts[0]);

            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_homepage'));
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
            $googleClientHelper->saveProfileId($request->request->get('profiles'));

            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_homepage'));
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
     * Return an ajax response
     *
     * @Route("/getOverview/{id}", requirements={"id" = "\d+"}, name="KunstmaanDashboardBundle_analytics_overview_ajax")
     *
     */
    public function getOverviewAction($id)
    {
        if ($id) {
            $em       = $this->getDoctrine()->getManager();
            $overview = $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getOverview($id);

            $extra['trafficDirectPercentage']       = $overview->getTrafficDirectPercentage();
            $extra['trafficReferralPercentage']     = $overview->getTrafficReferralPercentage();
            $extra['trafficSearchEnginePercentage'] = $overview->getTrafficSearchEnginePercentage();
            $extra['returningVisitsPercentage']     = $overview->getReturningVisitsPercentage();
            $extra['newVisitsPercentage']           = $overview->getNewVisitsPercentage();

            $extra['referrals'] = array();
            foreach ($overview->getReferrals()->toArray() as $key => $referral) {
                $extra['referrals'][$key]['visits'] = $referral->getVisits();
                $extra['referrals'][$key]['name']   = $referral->getName();
            }

            $extra['searches'] = array();
            foreach ($overview->getSearches()->toArray() as $key => $search) {
                $extra['searches'][$key]['visits']  = $search->getVisits();
                $extra['searches'][$key]['name']    = $search->getName();
            }

            $extra['campaigns'] = array();
            foreach ($overview->getCampaigns()->toArray() as $key => $search) {
                $extra['campaigns'][$key]['visits']  = $search->getVisits();
                $extra['campaigns'][$key]['name']    = $search->getName();
            }

            $extra['pages'] = array();
            foreach ($overview->getPages() as $key => $page) {
                $extra['pages'][$key]['visits']     = number_format($page->getVisits());
                $extra['pages'][$key]['name']       = $page->getName();
            }

            $extra['goals'] = array();
            foreach ($overview->getActiveGoals() as $key => $goal) {
                $extra['goals'][$key]['name']       = $goal->getName();
                $extra['goals'][$key]['visits']     = $goal->getVisits();
                $extra['goals'][$key]['id']         = $goal->getId();
                $extra['goals'][$key]['chart_data'] = json_decode($goal->getChartData());
            }

            $overviewData = array(
              'chartData'                           => json_decode($overview->getChartData()),
              'title'                               => $overview->getTitle(),
              'timespan'                            => $overview->getTimespan(),
              'startOffset'                         => $overview->getStartOffset(),
              'visits'                              => number_format($overview->getVisits()),
              'visitors'                            => number_format($overview->getVisitors()),
              'pagesPerVisit'                       => number_format($overview->getPagesPerVisit()),
              'avgVisitDuration'                    => number_format($overview->getAvgVisitDuration()),
              'returningVisits'                     => number_format($overview->getReturningVisits()),
              'newVisits'                           => number_format($overview->getNewVisits()),
              'bounceRate'                          => number_format($overview->getBounceRate()),
              'pageViews'                           => number_format($overview->getPageViews()),
              'trafficDirect'                       => number_format($overview->getTrafficDirect()),
              'trafficReferral'                     => number_format($overview->getTrafficReferral()),
              'trafficSearchEngine'                 => number_format($overview->getTrafficSearchEngine()),
              'desktopTraffic'                      => number_format($overview->getDesktopTraffic()),
              'mobileTraffic'                       => number_format($overview->getMobileTraffic()),
              'tabletTraffic'                       => number_format($overview->getTabletTraffic()),
              'desktopTrafficPercentage'            => $overview->getDesktopTrafficPercentage(),
              'mobileTrafficPercentage'             => $overview->getMobileTrafficPercentage(),
              'tabletTrafficPercentage'             => $overview->getTabletTrafficPercentage(),
            );

            $return = array(
              'responseCode'                        => 200,
              'overview'                            => $overviewData,
              'extra'                               => $extra
            );
        } else {
            $return = array(
              'responseCode'                        => 400
            );
        }

        return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
    }


    /**
     * Return an ajax response
     *
     * @Route("/getGoalChartData/{id}", requirements={"id" = "\d+"}, name="KunstmaanDashboardBundle_analytics_goalChartData_ajax")
     *
     */
    public function getGoalChartData($id) {

        $em            = $this->getDoctrine()->getManager();
        $chartData     = $em->getRepository('KunstmaanDashboardBundle:AnalyticsGoal')->getGoal($id)->getChartData();
        $name          = $em->getRepository('KunstmaanDashboardBundle:AnalyticsGoal')->getGoal($id)->getName();

        $return = array(
          'responseCode'  => 200,
          'chartData' => json_decode($chartData),
          'name' => $name,
        );

        return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * @Route("/resetProfile", name="KunstmaanDashboardBundle_analytics_resetProfile")
     */
    public function resetProfileAction()
    {
        $em            = $this->getDoctrine()->getManager();
        $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->resetProfileId();
        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_homepage'));
    }

    /**
     * @Route("/resetProperty", name="KunstmaanDashboardBundle_analytics_resetProperty")
     */
    public function resetPropertyAction()
    {
        $em            = $this->getDoctrine()->getManager();
        $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->resetPropertyId();
        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_homepage'));
    }

    /**
     * @Route("/updateData", name="KunstmaanDashboardBundle_analytics_update")
     */
    public function runUpdate()
    {
      $command = new UpdateAnalyticsOverviewCommand();
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


        // $results = $analyticsHelper->getResultsByDate(
        //   '2014-04-15',
        //   '2014-04-15',
        //   'ga:visitors',
        //   array('dimensions' => 'ga:hour, ga:date')
        // );


// dimensions=ga:pagePath
// metrics=ga:pageviews,ga:uniquePageviews,ga:timeOnPage,ga:bounces,ga:entrances,ga:exits
// sort=-ga:pageviews


                $extra = array(
                    'dimensions' => 'ga:yearWeek',
                    'sort' => 'ga:yearWeek'
                    );


        $results = $analyticsHelper->getResults(
          365,
          0,
          'ga:goal11Completions',
          $extra
        );




        $timestamp = strtotime(substr($results->getRows()[0][0], 0, 4) . 'W' . substr($results->getRows()[0][0], 4, 2));
        $timestamp = date('d/m/Y', $timestamp);

        echo $timestamp;

        var_dump($results->getRows());
        exit;
    }

}
