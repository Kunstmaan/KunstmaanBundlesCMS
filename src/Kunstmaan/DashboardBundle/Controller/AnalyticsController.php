<?php

namespace Kunstmaan\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

use Kunstmaan\AdminBundle\Command\UpdateAnalyticsOverviewCommand;

/**
 * The analytics controller
 */
class AnalyticsController extends Controller
{
    /**
     * @Route("/setToken/", name="KunstmaanAdminBundle_setToken")
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
                $googleClientHelper = $this->container->get('kunstmaan_admin.googleclienthelper');
            } catch (\Exception $e) {
                // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
                $currentRoute  = $request->attributes->get('_route');
                $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
                $params['url'] = $currentUrl . 'analytics/setToken/';

                return $this->render('KunstmaanAdminBundle:Analytics:connect.html.twig', $params);
            }

            $googleClientHelper->getClient()->authenticate();
            $googleClientHelper->saveToken($googleClientHelper->getClient()->getAccessToken());

            return $this->redirect($this->generateUrl('KunstmaanAdminBundle_PropertySelection'));
        }

        return $this->redirect($this->generateUrl('KunstmaanAdminBundle_homepage'));
    }

    /**
     * @Route("/selectWebsite", name="KunstmaanAdminBundle_PropertySelection")
     *
     * @param Request $request
     *
     * @return array
     */
    public function propertySelectionAction(Request $request)
    {
        // get API client
        try {
            $googleClientHelper = $this->container->get('kunstmaan_admin.googleclienthelper');
        } catch (\Exception $e) {
            // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
            $currentRoute  = $request->attributes->get('_route');
            $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'analytics/setToken/';

            return $this->render('KunstmaanAdminBundle:Analytics:connect.html.twig', $params);
        }

        if (null !== $request->request->get('properties')) {
            $parts = explode("::", $request->request->get('properties'));
            $googleClientHelper->saveAccountId($parts[1]);
            $googleClientHelper->savePropertyId($parts[0]);

            return $this->redirect($this->generateUrl('KunstmaanAdminBundle_homepage'));
        }

        /** @var GoogleClientHelper $googleClient */
        $googleClient    = $googleClientHelper->getClient();
        $analyticsHelper = $this->container->get('kunstmaan_admin.googleanalyticshelper');
        $analyticsHelper->init($googleClientHelper);
        $properties = $analyticsHelper->getProperties();

        return $this->render(
          'KunstmaanAdminBundle:Analytics:propertySelection.html.twig',
          array('properties' => $properties)
        );
    }

    /**
     * @Route("/selectProfile", name="KunstmaanAdminBundle_ProfileSelection")
     *
     * @param Request $request
     *
     * @return array
     */
    public function profileSelectionAction(Request $request)
    {
        // get API client
        try {
            $googleClientHelper = $this->container->get('kunstmaan_admin.googleclienthelper');
        } catch (\Exception $e) {
            // catch exception thrown by the googleClientHelper if one or more parameters in parameters.yml is not set
            $currentRoute  = $request->attributes->get('_route');
            $currentUrl    = $this->get('router')->generate($currentRoute, array(), true);
            $params['url'] = $currentUrl . 'analytics/setToken/';

            return $this->render('KunstmaanAdminBundle:Analytics:connect.html.twig', $params);
        }

        if (null !== $request->request->get('profiles')) {
            $googleClientHelper->saveProfileId($request->request->get('profiles'));

            return $this->redirect($this->generateUrl('KunstmaanAdminBundle_homepage'));
        }

        /** @var GoogleClientHelper $googleClient */
        $googleClient    = $googleClientHelper->getClient();
        $analyticsHelper = $this->container->get('kunstmaan_admin.googleanalyticshelper');
        $analyticsHelper->init($googleClientHelper);
        $profiles = $analyticsHelper->getProfiles();

        return $this->render(
          'KunstmaanAdminBundle:Analytics:profileSelection.html.twig',
          array('profiles' => $profiles)
        );
    }

    /**
     * Return an ajax response
     *
     * @Route("/getOverview/{id}", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_analytics_overview_ajax")
     *
     */
    public function getOverviewAction($id)
    {
        if ($id) {
            $em       = $this->getDoctrine()->getManager();
            $overview = $em->getRepository('KunstmaanAdminBundle:AnalyticsOverview')->getOverview($id);

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
            $extra['goals'] = array();
            foreach ($overview->getGoals()->toArray() as $key => $goal) {
                $extra['goals'][$key]['name']       = $goal->getName();
                $extra['goals'][$key]['visits']     = $goal->getVisits();
                $extra['goals'][$key]['id']         = $goal->getId();
            }

            $overviewData = array(
              'chartData'                           => json_decode($overview->getChartData()),
              'title'                               => $overview->getTitle(),
              'timespan'                            => $overview->getTimespan(),
              'startOffset'                         => $overview->getStartOffset(),
              'visits'                              => $overview->getVisits(),
              'returningVisits'                     => $overview->getReturningVisits(),
              'newVisits'                           => $overview->getNewVisits(),
              'bounceRate'                          => $overview->getBounceRate(),
              'pageViews'                           => $overview->getPageViews(),
              'trafficDirect'                       => $overview->getTrafficDirect(),
              'trafficReferral'                     => $overview->getTrafficReferral(),
              'trafficSearchEngine'                 => $overview->getTrafficSearchEngine(),
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
     * @Route("/getGoalChartData/{id}", requirements={"id" = "\d+"}, name="KunstmaanAdminBundle_analytics_goalChartData_ajax")
     *
     */
    public function getGoalChartData($id) {

        $em            = $this->getDoctrine()->getManager();
        $chartData     = $em->getRepository('KunstmaanAdminBundle:AnalyticsGoal')->getGoal($id)->getChartData();
        $name          = $em->getRepository('KunstmaanAdminBundle:AnalyticsGoal')->getGoal($id)->getName();

        $return = array(
          'responseCode'  => 200,
          'chartData' => json_decode($chartData),
          'name' => $name,
        );

        return new JsonResponse($return, 200, array('Content-Type' => 'application/json'));
    }

    /**
     * @Route("/resetProfile", name="KunstmaanAdminBundle_analytics_resetProfile")
     */
    public function resetProfileAction()
    {
        $em            = $this->getDoctrine()->getManager();
        $em->getRepository('KunstmaanAdminBundle:AnalyticsConfig')->resetProfileId();
        return $this->redirect($this->generateUrl('KunstmaanAdminBundle_homepage'));
    }

    /**
     * @Route("/resetProperty", name="KunstmaanAdminBundle_analytics_resetProperty")
     */
    public function resetPropertyAction()
    {
        $em            = $this->getDoctrine()->getManager();
        $em->getRepository('KunstmaanAdminBundle:AnalyticsConfig')->resetPropertyId();
        return $this->redirect($this->generateUrl('KunstmaanAdminBundle_homepage'));
    }

    /**
     * @Route("/updateData", name="KunstmaanAdminBundle_analytics_update")
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

}
