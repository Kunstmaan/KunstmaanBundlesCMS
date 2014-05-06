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

class GoogleAnalyticsAJAXController extends Controller
{

    /**
     * @Route("/updateData", name="KunstmaanDashboardBundle_analytics_update")
     */
    public function runUpdate(Request $request)
    {
        $configId = $request->query->get('configId');

        $command = new GoogleAnalyticsCommand();
        $command->setContainer($this->container);
        $input = new ArrayInput(array('configId' => $configId));
        $output = new NullOutput();
        $command->run($input, $output);

        return new JsonResponse(array(), 200, array('Content-Type' => 'application/json'));
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


}
