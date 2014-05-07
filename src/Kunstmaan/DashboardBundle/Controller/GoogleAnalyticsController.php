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

        // get the segment id
        $segmentId = $request->query->get('id');
        $params['segments'] = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->getConfig()->getSegments();
        $params['segmentId'] = $segmentId;

        // set the overviews param
        $params['token'] = true;
        if ($segmentId) {
            $overviews = $em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment')->getSegment($segmentId)->getOverviews();
        } else {
            $overviews = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->getDefaultOverviews();
        }

        $params['overviews'] = $overviews;
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
     * @Route("/config", name="KunstmaanDashboardBundle_AccountSelection")
     *
     * @param Request $request
     *
     * @return array
     */
    public function accountSelectionAction(Request $request)
    {
        $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

        if (null !== $request->request->get('accounts')) {
            return $this->redirect($this->generateUrl('kunstmaan_dashboard'));
        }

        $em = $this->getDoctrine()->getManager();
        $config = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->getConfig();
        $accounts = $configHelper->getAccounts();
        $segments = $config->getSegments();

        return $this->render(
            'KunstmaanDashboardBundle:Setup:setup.html.twig',
            array('accounts' => $accounts, 'segments' => $segments)
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
        $results = $query->getResults(
                1,1,
                'ga:visits',
                array(
                    'segment' => 'gaid::-7',
                    'dimensions' => 'ga:hour'
                )
            );

        $rows = $results->getRows();

        var_dump($rows);

        exit;

    }
}
