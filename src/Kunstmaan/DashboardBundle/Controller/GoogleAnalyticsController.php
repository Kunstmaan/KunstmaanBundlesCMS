<?php

namespace Kunstmaan\DashboardBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\ConfigHelper;
use Kunstmaan\DashboardBundle\Helper\Google\ClientHelper;
use Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class GoogleAnalyticsController extends AbstractController
{
    /** @var AnalyticsConfig */
    private $analyticsConfig;
    /** @var ClientHelper */
    private $clientHelper;
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(ConfigHelper $analyticsConfig, ClientHelper $clientHelper, EntityManagerInterface $em)
    {
        $this->analyticsConfig = $analyticsConfig;
        $this->clientHelper = $clientHelper;
        $this->em = $em;
    }

    /**
     * The index action will render the main screen the users see when they log in in to the admin
     */
    #[Route(path: '/', name: 'KunstmaanDashboardBundle_widget_googleanalytics')]
    public function widgetAction(Request $request): Response
    {
        $params['redirect_uri'] = $this->container->get('router')->generate('KunstmaanDashboardBundle_setToken', [], UrlGeneratorInterface::ABSOLUTE_URL);

        // if token not set
        if (!$this->analyticsConfig->tokenIsSet()) {
            if ($this->getParameter('kunstmaan_dashboard.google_analytics.api.client_id') != '' && $this->getParameter('kunstmaan_dashboard.google_analytics.api.client_secret') != '' && $this->getParameter('kunstmaan_dashboard.google_analytics.api.dev_key') != '') {
                $params['authUrl'] = $this->analyticsConfig->getAuthUrl();
            }

            return $this->render('@KunstmaanDashboard/GoogleAnalytics/connect.html.twig', $params);
        }

        // if propertyId not set
        if (!$this->analyticsConfig->accountIsSet()) {
            return $this->redirectToRoute('KunstmaanDashboardBundle_Config');
        }

        // if propertyId not set
        if (!$this->analyticsConfig->propertyIsSet()) {
            return $this->redirectToRoute('KunstmaanDashboardBundle_PropertySelection');
        }

        // if profileId not set
        if (!$this->analyticsConfig->profileIsSet()) {
            return $this->redirectToRoute('KunstmaanDashboardBundle_ProfileSelection');
        }

        // get the segment id
        $segmentId = $request->query->get('id');
        $params['segments'] = $this->em->getRepository(AnalyticsConfig::class)->findFirst()->getSegments();
        $params['segmentId'] = $segmentId;

        // set the overviews param
        $params['token'] = true;
        if ($segmentId) {
            $overviews = $this->em->getRepository(AnalyticsSegment::class)->find($segmentId)->getOverviews();
        } else {
            $overviews = $this->em->getRepository(AnalyticsOverview::class)->getDefaultOverviews();
        }

        $params['disableGoals'] = $this->em->getRepository(AnalyticsConfig::class)->findFirst()->getDisableGoals();
        $params['overviews'] = $overviews;
        /** @var AnalyticsConfigRepository $analyticsConfigRepository */
        $analyticsConfigRepository = $this->em->getRepository(AnalyticsConfig::class);
        $date = $analyticsConfigRepository->findFirst()->getLastUpdate();
        if ($date) {
            $params['last_update'] = $date->format('d-m-Y H:i');
        } else {
            $params['last_update'] = 'never';
        }

        return $this->render('@KunstmaanDashboard/GoogleAnalytics/widget.html.twig', $params);
    }

    #[Route(path: '/setToken/', name: 'KunstmaanDashboardBundle_setToken')]
    public function setTokenAction(Request $request): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $codeParameter = $request->query->get('code');

        if (null !== $codeParameter) {
            $code = urldecode($codeParameter);

            $this->clientHelper->getClient()->authenticate($code);
            $this->analyticsConfig->saveToken($this->clientHelper->getClient()->getAccessToken());

            return $this->redirectToRoute('KunstmaanDashboardBundle_Config');
        }

        return $this->redirectToRoute('KunstmaanDashboardBundle_widget_googleanalytics');
    }

    #[Route(path: '/config', name: 'KunstmaanDashboardBundle_Config')]
    public function configAction(Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $params = [];

        if (null !== $request->request->get('accounts')) {
            return $this->redirectToRoute('kunstmaan_dashboard');
        }

        $config = $this->em->getRepository(AnalyticsConfig::class)->findFirst();

        $params['accountId'] = $config->getAccountId();
        $params['propertyId'] = 0;
        $params['profileId'] = 0;
        $params['properties'] = [];
        $params['profiles'] = [];

        if ($params['accountId']) {
            $params['propertyId'] = $config->getPropertyId();
            $params['properties'] = $this->analyticsConfig->getProperties();

            $params['profileId'] = $config->getProfileId();
            $params['profiles'] = $this->analyticsConfig->getProfiles();
        }

        $params['accounts'] = $this->analyticsConfig->getAccounts();
        $params['segments'] = $config->getSegments();
        $params['disableGoals'] = $config->getDisableGoals();
        $params['configId'] = $config->getId();

        $params['profileSegments'] = $this->analyticsConfig->getProfileSegments();

        return $this->render('@KunstmaanDashboard/GoogleAnalytics/setupcontainer.html.twig', $params);
    }

    #[Route(path: '/resetProfile', name: 'KunstmaanDashboardBundle_analytics_resetProfile')]
    public function resetProfileAction(): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->em->getRepository(AnalyticsConfig::class)->resetProfileId();

        return $this->redirectToRoute('KunstmaanDashboardBundle_ProfileSelection');
    }

    #[Route(path: '/resetProperty', name: 'KunstmaanDashboardBundle_analytics_resetProperty')]
    public function resetPropertyAction(): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $this->em->getRepository(AnalyticsConfig::class)->resetPropertyId();

        return $this->redirectToRoute('KunstmaanDashboardBundle_Config');
    }
}
