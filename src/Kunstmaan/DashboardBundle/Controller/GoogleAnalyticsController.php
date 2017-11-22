<?php
namespace Kunstmaan\DashboardBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\ConfigHelper;
use Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class GoogleAnalyticsController
 * @package Kunstmaan\DashboardBundle\Controller
 */
class GoogleAnalyticsController extends Controller
{
    /** @var ObjectManager $em */
    protected $em;

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="KunstmaanDashboardBundle_widget_googleanalytics")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @return Response|array
     */
    public function widgetAction(Request $request)
    {
        $params['redirect_uri'] = $this->get('router')->generate('KunstmaanDashboardBundle_setToken', [], UrlGeneratorInterface::ABSOLUTE_URL);
        /** @var ConfigHelper $configHelper */
        $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

        switch (false) {
            case $configHelper->tokenIsSet() :
                return $this->connectToGoogle($configHelper, $params);
            case $configHelper->accountIsSet() :
                return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_Config'));
            case $configHelper->propertyIsSet() :
                return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_PropertySelection'));
            case $configHelper->profileIsSet() :
                return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
        }

        $this->em = $this->getDoctrine()->getManager();
        $segmentId = $request->query->get('id');
        $overviews = $this->getOverviews($segmentId);
        /** @var AnalyticsConfigRepository $analyticsConfigRepository */
        $analyticsConfigRepository = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $params['segmentId'] = $segmentId;
        $params['token'] = true;
        $params['overviews'] = $overviews;
        $params['disableGoals'] = $analyticsConfigRepository->findFirst()->getDisableGoals();
        $params['segments'] = $analyticsConfigRepository->findFirst()->getSegments();
        $date = $analyticsConfigRepository->findFirst()->getLastUpdate();
        $params['last_update'] = ($date) ? $date->format('d-m-Y H:i') : 'never';

        return $params;
    }

    /**
     * @param null|int $segmentId
     * @return mixed
     */
    private function getOverviews($segmentId = null)
    {
        if ($segmentId) {
            $overviews = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment')->find($segmentId)->getOverviews();
        } else {
            $overviews = $this->em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getDefaultOverviews();
        }
        return $overviews;
    }


    /**
     * @param ConfigHelper $configHelper
     * @param array $params
     * @return Response
     */
    private function connectToGoogle(ConfigHelper $configHelper, array $params)
    {
        if ($this->getParameter('google.api.client_id') != '' && $this->getParameter('google.api.client_secret') != '' && $this->getParameter('google.api.dev_key') != '' ) {
            $params['authUrl'] = $configHelper->getAuthUrl($params['redirect_uri']);
        }

        return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
    }


    /**
     * @Route("/setToken/", name="KunstmaanDashboardBundle_setToken")
     *
     * @param Request $request
     *
     * @throws AccessDeniedException
     *
     * @return array|Response
     */
    public function setTokenAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $code = urldecode($request->query->get('code'));

        if (isset($code)) {
            $clientHelper = $this->container->get('kunstmaan_dashboard.helper.google.client');
            $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

            $clientHelper->getClient()->authenticate($code);
            $configHelper->saveToken($clientHelper->getClient()->getAccessToken());

            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_Config'));
        }

        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_widget_googleanalytics'));
    }


    /**
     * @Route("/config", name="KunstmaanDashboardBundle_Config")
     *
     * @param Request $request
     *
     * @throws AccessDeniedException
     *
     * @return array|Response
     */
    public function configAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if (null !== $request->request->get('accounts')) {
            return $this->redirect($this->generateUrl('kunstmaan_dashboard'));
        }

        /** @var ConfigHelper $configHelper */
        $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');
        $em = $this->getDoctrine()->getManager();
        /** @var AnalyticsConfig $config */
        $config = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->findFirst();
        $params = $this->getConfigParams($configHelper, $config);

        return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:setupcontainer.html.twig', $params);
    }

    /**
     * @param ConfigHelper $configHelper
     * @param AnalyticsConfig $config
     * @return array
     */
    private function getConfigParams(ConfigHelper $configHelper, AnalyticsConfig $config)
    {
        $params = [];
        $params['accountId'] = $config->getAccountId();
        $params['propertyId'] = 0;
        $params['profileId'] = 0;
        $params['properties'] = [];
        $params['profiles'] = [];
        $params['accounts'] = $configHelper->getAccounts();
        $params['segments'] = $config->getSegments();
        $params['disableGoals'] = $config->getDisableGoals();
        $params['configId'] = $config->getId();
        $params['profileSegments'] = $configHelper->getProfileSegments();

        if ($params['accountId']) {
            $params['propertyId'] = $config->getPropertyId();
            $params['properties'] = $configHelper->getProperties();

            $params['profileId'] = $config->getProfileId();
            $params['profiles'] = $configHelper->getProfiles();
        }

        return $params;
    }

    /**
     * @Route("/resetProfile", name="KunstmaanDashboardBundle_analytics_resetProfile")
     *
     * @throws AccessDeniedException
     */
    public function resetProfileAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->resetProfileId();
        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
    }

    /**
     * @Route("/resetProperty", name="KunstmaanDashboardBundle_analytics_resetProperty")
     *
     * @throws AccessDeniedException
     */
    public function resetPropertyAction()
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->resetPropertyId();
        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_Config'));
    }
}
