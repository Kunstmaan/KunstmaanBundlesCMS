<?php

namespace Kunstmaan\DashboardBundle\Controller;

use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;
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
 */
class GoogleAnalyticsController extends Controller
{
    /**
     * The index action will render the main screen the users see when they log in in to the admin
     *
     * @Route("/", name="KunstmaanDashboardBundle_widget_googleanalytics")
     * @Template()
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return Response|array
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function widgetAction(Request $request)
    {
        $params['redirect_uri'] = $this->get('router')->generate('KunstmaanDashboardBundle_setToken', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

        // if token not set
        if (!$configHelper->tokenIsSet()) {
            if ($this->getParameter('google.api.client_id') !== '' && $this->getParameter('google.api.client_secret') !== '' && $this->getParameter(
                    'google.api.dev_key'
                ) !== '') {
                $params['authUrl'] = $configHelper->getAuthUrl();
            }

            return $this->render('KunstmaanDashboardBundle:GoogleAnalytics:connect.html.twig', $params);
        }

        if (!$configHelper->accountIsSet() || !$configHelper->propertyIsSet() || !$configHelper->profileIsSet()) {
            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_Config'));
        }

        $em = $this->getDoctrine()->getManager();

        // get the segment id
        $segmentId = $request->query->get('id');
        $params['segments'] = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->findFirst()->getSegments();
        $params['segmentId'] = $segmentId;

        // set the overviews param
        $params['token'] = true;
        if ($segmentId) {
            $overviews = $em->getRepository(AnalyticsSegment::class)->find($segmentId)->getOverviews();
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
            $params['last_update'] = 'never';
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
     * @return Response
     */
    public function setTokenAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $code = urldecode($request->query->get('code'));

        if (null !== $code) {
            $clientHelper = $this->container->get('kunstmaan_dashboard.helper.google.client');
            $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

            $clientHelper->getClient()->fetchAccessTokenWithAuthCode($code);
            if (null !== ($accessToken = $clientHelper->getClient()->getAccessToken())) {
                $configHelper->saveToken(json_encode($accessToken));
            }

            return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_Config'));
        }

        return $this->redirect($this->generateUrl('KunstmaanDashboardBundle_widget_googleanalytics'));
    }


    /**
     * @Route("/config", name="KunstmaanDashboardBundle_Config")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function configAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $params = [];
        $configHelper = $this->container->get('kunstmaan_dashboard.helper.google.analytics.config');

        if (null !== $request->request->get('accounts')) {
            return $this->redirect($this->generateUrl('kunstmaan_dashboard'));
        }

        $em = $this->getDoctrine()->getManager();
        $config = $em->getRepository('KunstmaanDashboardBundle:AnalyticsConfig')->findFirst();

        $params['accountId'] = $config->getAccountId();
        $params['propertyId'] = 0;
        $params['profileId'] = 0;
        $params['properties'] = [];
        $params['profiles'] = [];

        if ($params['accountId']) {
            $params['propertyId'] = $config->getPropertyId();
            $params['properties'] = $configHelper->getProperties();

            $params['profileId'] = $config->getProfileId();
            $params['profiles'] = $configHelper->getProfiles();
        }

        $params['accounts'] = $configHelper->getAccounts();
        $params['segments'] = $config->getSegments();
        $params['disableGoals'] = $config->getDisableGoals();
        $params['configId'] = $config->getId();


        $params['profileSegments'] = $configHelper->getProfileSegments();

        return $this->render(
            'KunstmaanDashboardBundle:GoogleAnalytics:setupcontainer.html.twig',
            $params
        );
    }
}
