<?php
namespace Kunstmaan\DashboardBundle\Controller;

use Kunstmaan\DashboardBundle\Helper\Google\Analytics\ConfigHelper;
use Kunstmaan\DashboardBundle\Helper\Google\ClientHelper;
use Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GoogleAnalyticsController extends AbstractController
{
    /**
     * @var ConfigHelper
     */
    protected $configHelper;

    /**
     * @var ClientHelper
     */
    protected $clientHelper;

    /**
     * @required
     * @param ConfigHelper $configHelper
     */
    public function setConfigHelper(ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * @required
     * @param ClientHelper $clientHelper
     */
    public function setClientHelper(ClientHelper $clientHelper)
    {
        $this->clientHelper = $clientHelper;
    }

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
        $params['redirect_uri'] = $this->generateUrl('KunstmaanDashboardBundle_setToken', array(), UrlGeneratorInterface::ABSOLUTE_URL);

        // if token not set
        if (!$this->configHelper->tokenIsSet()) {
            if ($this->container->getParameter('google.api.client_id') != '' && $this->container->getParameter('google.api.client_secret') != '' && $this->container->getParameter('google.api.dev_key') != '' ) {
                $params['authUrl'] = $this->configHelper->getAuthUrl($params['redirect_uri']);
            }

            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        // if propertyId not set
        if (!$this->configHelper->accountIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_Config'));
        }

        // if propertyId not set
        if (!$this->configHelper->propertyIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_PropertySelection'));
        }

        // if profileId not set
        if (!$this->configHelper->profileIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_ProfileSelection'));
        }

        $em = $this->getDoctrine()->getManager();

        // get the segment id
        $segmentId = $request->query->get('id');
        $params['segments'] = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->findFirst()->getSegments();
        $params['segmentId'] = $segmentId;

        // set the overviews param
        $params['token'] = true;
        if ($segmentId) {
            $overviews = $em->getRepository('KunstmaanDashboardBundle:AnalyticsSegment')->find($segmentId)->getOverviews();
        } else {
            $overviews = $em->getRepository('KunstmaanDashboardBundle:AnalyticsOverview')->getDefaultOverviews();
        }

        $params['disableGoals'] = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->findFirst()->getDisableGoals();
        $params['overviews'] = $overviews;
        /** @var AnalyticsConfigRepository $analyticsConfigRepository */
        $analyticsConfigRepository = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig');
        $date = $analyticsConfigRepository->findFirst()->getLastUpdate();
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
     * @throws AccessDeniedException
     *
     * @return array
     */
    public function setTokenAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $code = urldecode($request->query->get('code'));

        if (isset($code)) {
            $this->clientHelper->getClient()->authenticate($code);
            $this->configHelper->saveToken($this->clientHelper->getClient()->getAccessToken());

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
     * @return array
     */
    public function configAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $params = array();

        if (null !== $request->request->get('accounts')) {
            return $this->redirect($this->generateUrl('kunstmaan_dashboard'));
        }

        $em = $this->getDoctrine()->getManager();
        $config = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->findFirst();

        $params['accountId'] = $config->getAccountId();
        $params['propertyId'] = 0;
        $params['profileId'] = 0;
        $params['properties'] = array();
        $params['profiles'] = array();

        if ($params['accountId']) {
            $params['propertyId'] = $config->getPropertyId();
            $params['properties'] = $this->configHelper->getProperties();

            $params['profileId'] = $config->getProfileId();
            $params['profiles'] = $this->configHelper->getProfiles();
        }

        $params['accounts'] = $this->configHelper->getAccounts();
        $params['segments'] = $config->getSegments();
        $params['disableGoals'] = $config->getDisableGoals();
        $params['configId'] = $config->getId();


        $params['profileSegments'] = $this->configHelper->getProfileSegments();

        return $this->render(
            'KunstmaanDashboardBundle:GoogleAnalytics:setupcontainer.html.twig',
            $params
        );
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
