<?php

namespace Kunstmaan\DashboardBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\FlashMessages\FlashTypes;
use Kunstmaan\DashboardBundle\Entity\AnalyticsConfig;
use Kunstmaan\DashboardBundle\Entity\AnalyticsGoal;
use Kunstmaan\DashboardBundle\Entity\AnalyticsOverview;
use Kunstmaan\DashboardBundle\Entity\AnalyticsSegment;
use Kunstmaan\DashboardBundle\Helper\Google\Analytics\ConfigHelper;
use Kunstmaan\DashboardBundle\Repository\AnalyticsOverviewRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

final class GoogleAnalyticsAJAXController extends AbstractController
{
    /** @var KernelInterface */
    private $kernel;
    /** @var ConfigHelper */
    private $analyticsConfig;
    /** @var EntityManagerInterface */
    private $em;
    /** @var TranslatorInterface */
    private $translator;

    public function __construct(KernelInterface $kernel, ConfigHelper $analyticsConfig, EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->kernel = $kernel;
        $this->analyticsConfig = $analyticsConfig;
        $this->em = $em;
        $this->translator = $translator;
    }

    /**
     * @Route("/updateData", name="KunstmaanDashboardBundle_analytics_update")
     */
    public function runUpdateAction(Request $request)
    {
        $configId = $request->query->get('configId');
        $segmentId = $request->query->get('segmentId');

        $application = new Application($this->kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'kuma:dashboard:widget:googleanalytics:data:collect',
            '--config' => $configId,
            '--segment' => $segmentId,
        ]);

        $application->run($input, new NullOutput());

        return new JsonResponse([], 200, ['Content-Type' => 'application/json']);
    }

    /**
     * Return an ajax response with all data for an overview
     *
     * @Route("/getOverview/{id}", requirements={"id" = "\d+"}, name="KunstmaanDashboardBundle_analytics_overview_ajax")
     */
    public function getOverviewAction($id)
    {
        /** @var AnalyticsOverviewRepository $analyticsOverviewRepository */
        $analyticsOverviewRepository = $this->em->getRepository(AnalyticsOverview::class);
        $overview = $analyticsOverviewRepository->find($id);

        // goals data
        $goals = [];
        foreach ($overview->getActiveGoals() as $key => $goal) {
            /* @var AnalyticsGoal $goal */
            $goals[$key]['name'] = $goal->getName();
            $goals[$key]['visits'] = $goal->getVisits();
            $goals[$key]['id'] = $goal->getId();
            $goals[$key]['chartData'] = json_decode($goal->getChartData());
        }

        // overview data
        $overviewData = [
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
        ];

        // put all data in the return array
        $return = [
            'responseCode' => 200,
            'overview' => $overviewData,
            'goals' => $goals,
        ];

        // return json response
        return new JsonResponse($return, 200, ['Content-Type' => 'application/json']);
    }

    /* =============================== ACCOUNT =============================== */

    /**
     * @Route("/accounts/", name="kunstmaan_dashboard_ajax_accounts")
     */
    public function getAccountsAction(Request $request)
    {
        $configId = $request->query->get('configId');
        if ($configId) {
            $this->analyticsConfig->init($configId);
        }

        $accounts = $this->analyticsConfig->getAccounts();

        return new JsonResponse($accounts, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/account/save", name="kunstmaan_dashboard_ajax_account_save")
     */
    public function saveAccountAction(Request $request)
    {
        $accountId = $request->query->get('id');
        $this->analyticsConfig->saveAccountId($accountId);

        return new JsonResponse();
    }

    /* =============================== PROPERTY =============================== */

    /**
     * @Route("/properties/", name="kunstmaan_dashboard_ajax_properties")
     */
    public function getPropertiesAction(Request $request)
    {
        $accountId = $request->query->get('accountId');
        $configId = $request->query->get('configId');
        if ($configId) {
            $this->analyticsConfig->init($configId);
        }

        $properties = $this->analyticsConfig->getProperties($accountId);

        return new JsonResponse($properties, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/property/save", name="kunstmaan_dashboard_ajax_property_save")
     */
    public function savePropertyAction(Request $request)
    {
        $propertyId = $request->query->get('id');
        $this->analyticsConfig->savePropertyId($propertyId);

        return new JsonResponse();
    }

    /* =============================== PROFILE =============================== */

    /**
     * @Route("/profiles/", name="kunstmaan_dashboard_ajax_profiles")
     */
    public function getProfilesAction(Request $request)
    {
        $accountId = $request->query->get('accountId');
        $propertyId = $request->query->get('propertyId');
        $configId = $request->query->get('configId');
        if ($configId) {
            $this->analyticsConfig->init($configId);
        }

        $profiles = $this->analyticsConfig->getProfiles($accountId, $propertyId);

        return new JsonResponse($profiles, 200, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/profile/save", name="kunstmaan_dashboard_ajax_profile_save")
     */
    public function saveProfileAction(Request $request)
    {
        $propertyId = $request->query->get('id');
        $this->analyticsConfig->saveProfileId($propertyId);

        return new JsonResponse();
    }

    /* =============================== CONFIG =============================== */

    /**
     * @Route("/config/save", name="kunstmaan_dashboard_ajax_config_save")
     */
    public function saveConfigAction(Request $request)
    {
        // get params
        $configId = $request->query->get('configId');
        $accountId = $request->query->get('accountId');
        $propertyId = $request->query->get('propertyId');
        $profileId = $request->query->get('profileId');
        $disableGoals = $request->query->get('disableGoals');

        // edit the config
        $config = $this->em->getRepository(AnalyticsConfig::class)->find($configId);
        if ($accountId && $propertyId && $profileId) {
            $config->setAccountId($accountId);
            $config->setPropertyId($propertyId);
            $config->setProfileId($profileId);
        }

        $this->em->persist($config);
        $this->em->flush();

        // set the config name
        $this->analyticsConfig->init($configId);
        $profile = $this->analyticsConfig->getActiveProfile();
        $config->setName($profile['profileName']);
        $config->setDisableGoals($disableGoals);

        $this->em->persist($config);
        $this->em->flush();

        $this->addFlash(
            FlashTypes::SUCCESS,
            $this->translator->trans('kuma_admin.ga_ajax_controller.flash.success')
        );

        return new JsonResponse();
    }

    /**
     * @Route("/config/remove", name="kunstmaan_dashboard_ajax_config_remove")
     */
    public function removeConfigAction(Request $request)
    {
        // get params
        $configId = $request->query->get('configId');

        // edit the config
        $config = $this->em->getRepository(AnalyticsConfig::class)->find($configId);
        $this->em->remove($config);
        $this->em->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/config/get", name="kunstmaan_dashboard_ajax_config_get")
     */
    public function getConfigAction(Request $request)
    {
        $config = $this->em->getRepository(AnalyticsConfig::class)->findFirst();
        $accountId = $config->getAccountId();

        if (!$accountId) {
            return new JsonResponse();
        }

        $this->analyticsConfig->getAccounts();
        $this->analyticsConfig->getProperties();
        $this->analyticsConfig->getProfiles();
    }

    /* =============================== SEGMENT =============================== */

    /**
     * @Route("/segment/add/", name="kunstmaan_dashboard_ajax_segment_add")
     */
    public function addSegmentAction(Request $request)
    {
        $configId = $request->query->get('configId');

        // create a new segment
        $segment = new AnalyticsSegment();
        $query = $request->query->get('query');
        $name = $request->query->get('name');
        $segment->setQuery($query);
        $segment->setName($name);

        // add the segment to the config
        $config = $this->em->getRepository(AnalyticsConfig::class)->find($configId);
        $segment->setConfig($config);
        $segments = $config->getSegments();
        $segments[] = $segment;
        $config->setSegments($segments);

        $this->em->persist($config);
        $this->em->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/segment/delete", name="kunstmaan_dashboard_ajax_segment_delete")
     */
    public function deleteSegmentAction(Request $request)
    {
        $id = $request->query->get('id');
        $this->em->getRepository(AnalyticsSegment::class)->deleteSegment($id);

        return new JsonResponse();
    }

    /**
     * @Route("/segment/edit", name="kunstmaan_dashboard_ajax_segment_edit")
     */
    public function editSegmentAction(Request $request)
    {
        $id = $request->query->get('id');
        $query = $request->query->get('query');
        $name = $request->query->get('name');
        $segment = $this->em->getRepository(AnalyticsSegment::class)->find($id);
        $segment->setName($name);
        $segment->setQuery($query);
        $this->em->persist($segment);
        $this->em->flush();

        return new JsonResponse();
    }
}
