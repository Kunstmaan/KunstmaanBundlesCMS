<?php

namespace Kunstmaan\DashboardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnalyticsOverview
 *
 * @ORM\Table(name="kuma_analytics_overview")
 * @ORM\Entity(repositoryClass="Kunstmaan\DashboardBundle\Repository\AnalyticsOverviewRepository")
 */
class AnalyticsOverview extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{

    /**
     * Get percentage of direct traffic
     *
     * @return int
     */
    public function getTrafficDirectPercentage() {
        return $this->visits ? round(($this->trafficDirect / $this->visits) * 100) : 0;
    }

    /**
     * Get percentage of referral traffic
     *
     * @return int
     */
    public function getTrafficReferralPercentage() {
        return $this->visits ? round(($this->trafficReferral / $this->visits) * 100) : 0;
    }

    /**
     * Get percentage of search engine traffic
     *
     * @return int
     */
    public function getTrafficSearchEnginePercentage() {
        return $this->visits ? round(($this->trafficSearchEngine / $this->visits) * 100) : 0;
    }

    /**
     * Get percentage of returning visits
     *
     * @return int
     */
    public function getReturningVisitsPercentage() {
        return $this->returningVisits ? round(($this->returningVisits / $this->visits) * 100) : 0;
    }

    /**
     * Get percentage of new visits
     *
     * @return int
     */
    public function getNewVisitsPercentage() {
        return $this->newVisits ? round(($this->newVisits / $this->visits) * 100) : 0;
    }

    /**
     * Get percentage of desktop traffic
     *
     * @return int
     */
    public function getDesktopTrafficPercentage() {
        return $this->visitors ? round(($this->desktopTraffic / $this->visitors) * 100) : 0;
    }

    /**
     * Get percentage of mobile traffic
     *
     * @return int
     */
    public function getMobileTrafficPercentage() {
        return $this->visitors ? round(($this->mobileTraffic / $this->visitors) * 100) : 0;
    }

    /**
     * Get percentage of tablet traffic
     *
     * @return int
     */
    public function getTabletTrafficPercentage() {
        return $this->visitors ? round(($this->tabletTraffic / $this->visitors) * 100) : 0;
    }





    /**
     * @ORM\OneToMany(targetEntity="AnalyticsCampaign", mappedBy="overview", cascade={"persist"})
     */
    private $campaigns;

    /**
     * @ORM\OneToMany(targetEntity="AnalyticsTopReferral", mappedBy="overview", cascade={"persist"})
     */
    private $referrals;

    /**
     * @ORM\OneToMany(targetEntity="AnalyticsGoal", mappedBy="overview", cascade={"persist"})
     */
    private $goals;

    /**
     * @ORM\OneToMany(targetEntity="AnalyticsTopSearch", mappedBy="overview", cascade={"persist"})
     */
    private $searches;

    /**
     * @ORM\OneToMany(targetEntity="AnalyticsTopPage", mappedBy="overview", cascade={"persist"})
     */
    private $pages;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var integer
     *
     * @ORM\Column(name="timespan", type="integer")
     */
    private $timespan;

    /**
     * @var integer
     *
     * @ORM\Column(name="start_days_ago", type="integer")
     */
    private $startOffset = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="visits", type="integer")
     */
    private $visits = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="visitors", type="integer")
     */
    private $visitors = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="returning_visits", type="integer")
     */
    private $returningVisits = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="new_visits", type="integer")
     */
    private $newVisits = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pageviews", type="integer")
     */
    private $pageviews = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="traffic_direct", type="integer")
     */
    private $trafficDirect = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="traffic_referral", type="integer")
     */
    private $trafficReferral = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="traffic_search_engine", type="integer")
     */
    private $trafficSearchEngine = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="bounce_rate", type="integer")
     */
    private $bounceRate = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pages_per_visit", type="integer")
     */
    private $pagesPerVisit = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="avg_visit_duration", type="integer")
     */
    private $avgVisitDuration = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="desktop_traffic", type="integer")
     */
    private $desktopTraffic = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="tablet_traffic", type="integer")
     */
    private $tabletTraffic = 0;


    /**
     * @var integer
     *
     * @ORM\Column(name="mobile_traffic", type="integer")
     */
    private $mobileTraffic = 0;




    /**
     * Set campaigns
     *
     * @param array $campaigns
     * @return AnalyticsDailyOverview
     */
    public function setCampaigns($campaigns)
    {
        $this->campaigns = $campaigns;

        return $this;
    }

    /**
     * Get campaigns
     *
     * @return array
     */
    public function getCampaigns()
    {
        return $this->campaigns;
    }

    /**
     * Set referrals
     *
     * @param array $referrals
     * @return AnalyticsDailyOverview
     */
    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;

        return $this;
    }

    /**
     * Get referrals
     *
     * @return array
     */
    public function getReferrals()
    {
        return $this->referrals;
    }

    /**
     * Set goals
     *
     * @param array $goals
     * @return AnalyticsDailyOverview
     */
    public function setGoals($goals)
    {
        $this->goals = $goals;

        return $this;
    }

    /**
     * Get goals
     *
     * @return array
     */
    public function getGoals()
    {
        return $this->goals;
    }

    public function getActiveGoals() {
        $goals = array();
        foreach ($this->goals->toArray() as $goal) {
            if ($goal->getVisits()) $goals[] = $goal;
        }
        return $goals;
    }

    /**
     * Set searches
     *
     * @param array $searches
     * @return AnalyticsDailyOverview
     */
    public function setSearches($searches)
    {
        $this->searches = $searches;

        return $this;
    }

    /**
     * Get searches
     *
     * @return array
     */
    public function getSearches()
    {
        return $this->searches;
    }

    /**
     * Set pages
     *
     * @param array $pages
     * @return AnalyticsDailyOverview
     */
    public function setPages($pages)
    {
        $this->pages = $pages;

        return $this;
    }

    /**
     * Get pages
     *
     * @return array
     */
    public function getPages()
    {
        return $this->pages;
    }


    /**
     * @var array
     *
     * @ORM\Column(name="chart_data", type="text")
     */
    private $chartData = '';


    /**
     * Set chartData
     *
     * @param array $chartData
     * @return AnalyticsDailyOverview
     */
    public function setChartData($chartData)
    {
        $this->chartData = $chartData;

        return $this;
    }

    /**
     * Get chartData
     *
     * @return array
     */
    public function getChartData()
    {
        return $this->chartData;
    }

    /**
     * Set trafficSearchEngine
     *
     * @param string $trafficSearchEngine
     * @return AnalyticsOverview
     */
    public function setTrafficSearchEngine($trafficSearchEngine)
    {
        $this->trafficSearchEngine = $trafficSearchEngine;

        return $this;
    }

    /**
     * Get trafficSearchEngine
     *
     * @return string
     */
    public function getTrafficSearchEngine()
    {
        return $this->trafficSearchEngine;
    }

    /**
     * Set trafficReferral
     *
     * @param string $trafficReferral
     * @return AnalyticsOverview
     */
    public function setTrafficReferral($trafficReferral)
    {
        $this->trafficReferral = $trafficReferral;

        return $this;
    }

    /**
     * Get trafficReferral
     *
     * @return string
     */
    public function getTrafficReferral()
    {
        return $this->trafficReferral;
    }

    /**
     * Set trafficDirect
     *
     * @param string $trafficDirect
     * @return AnalyticsOverview
     */
    public function setTrafficDirect($trafficDirect)
    {
        $this->trafficDirect = $trafficDirect;

        return $this;
    }

    /**
     * Get trafficDirect
     *
     * @return string
     */
    public function getTrafficDirect()
    {
        return $this->trafficDirect;
    }

    /**
     * Set newVisits
     *
     * @param string $newVisits
     * @return AnalyticsOverview
     */
    public function setNewVisits($newVisits)
    {
        $this->newVisits = $newVisits;

        return $this;
    }

    /**
     * Get newVisits
     *
     * @return string
     */
    public function getNewVisits()
    {
        return $this->newVisits;
    }


    /**
     * Set returningVisits
     *
     * @param string $returningVisits
     * @return AnalyticsOverview
     */
    public function setReturningVisits($returningVisits)
    {
        $this->returningVisits = $returningVisits;

        return $this;
    }

    /**
     * Get returningVisits
     *
     * @return string
     */
    public function getReturningVisits()
    {
        return $this->returningVisits;
    }


    /**
     * Set title
     *
     * @param string $title
     * @return AnalyticsOverview
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set startOffset
     *
     * @param integer $startOffset
     * @return AnalyticsOverview
     */
    public function setStartOffset($startOffset)
    {
        $this->startOffset = $startOffset;

        return $this;
    }

    /**
     * Get startOffset
     *
     * @return integer
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * Set timespan
     *
     * @param integer $timespan
     * @return AnalyticsOverview
     */
    public function setTimespan($timespan)
    {
        $this->timespan = $timespan;

        return $this;
    }

    /**
     * Get timespan
     *
     * @return integer
     */
    public function getTimespan()
    {
        return $this->timespan;
    }

    /**
     * Set visits
     *
     * @param integer $visits
     * @return AnalyticsOverview
     */
    public function setVisits($visits)
    {
        $this->visits = $visits;

        return $this;
    }

    /**
     * Get visits
     *
     * @return integer
     */
    public function getVisitors()
    {
        return $this->visitors;
    }

    /**
     * Set visitors
     *
     * @param integer $visitors
     * @return AnalyticsOverview
     */
    public function setVisitors($visitors)
    {
        $this->visitors = $visitors;

        return $this;
    }

    /**
     * Get visitors
     *
     * @return integer
     */
    public function getVisits()
    {
        return $this->visits;
    }

    /**
     * Set pageviews
     *
     * @param integer $pageviews
     * @return AnalyticsOverview
     */
    public function setPageviews($pageviews)
    {
        $this->pageviews = $pageviews;

        return $this;
    }

    /**
     * Get pageviews
     *
     * @return integer
     */
    public function getPageviews()
    {
        return $this->pageviews;
    }

    /**
     * Set bounceRate
     *
     * @param integer $bounceRate
     * @return AnalyticsOverview
     */
    public function setBounceRate($bounceRate)
    {
        $this->bounceRate = $bounceRate;

        return $this;
    }

    /**
     * Get bounceRate
     *
     * @return integer
     */
    public function getBounceRate()
    {
        return $this->bounceRate;
    }

    /**
     * Set pagesPerVisit
     *
     * @param integer $pagesPerVisit
     * @return AnalyticsOverview
     */
    public function setPagesPerVisit($pagesPerVisit)
    {
        $this->pagesPerVisit = $pagesPerVisit;

        return $this;
    }

    /**
     * Get pagesPerVisit
     *
     * @return integer
     */
    public function getPagesPerVisit()
    {
        return $this->pagesPerVisit;
    }

    /**
     * Set avgVisitDuration
     *
     * @param integer $avgVisitDuration
     * @return AnalyticsOverview
     */
    public function setAvgVisitDuration($avgVisitDuration)
    {
        $this->avgVisitDuration = $avgVisitDuration;

        return $this;
    }

    /**
     * Get avgVisitDuration
     *
     * @return integer
     */
    public function getAvgVisitDuration()
    {
        return $this->avgVisitDuration;
    }

    /**
     * Set mobileTraffic
     *
     * @param integer $mobileTraffic
     * @return AnalyticsOverview
     */
    public function setMobileTraffic($mobileTraffic)
    {
        $this->mobileTraffic = $mobileTraffic;

        return $this;
    }

    /**
     * Get mobileTraffic
     *
     * @return integer
     */
    public function getMobileTraffic()
    {
        return $this->mobileTraffic;
    }

    /**
     * Set tabletTraffic
     *
     * @param integer $tabletTraffic
     * @return AnalyticsOverview
     */
    public function setTabletTraffic($tabletTraffic)
    {
        $this->tabletTraffic = $tabletTraffic;

        return $this;
    }

    /**
     * Get tabletTraffic
     *
     * @return integer
     */
    public function getTabletTraffic()
    {
        return $this->tabletTraffic;
    }

    /**
     * Set desktopTraffic
     *
     * @param integer $desktopTraffic
     * @return AnalyticsOverview
     */
    public function setDesktopTraffic($desktopTraffic)
    {
        $this->desktopTraffic = $desktopTraffic;

        return $this;
    }

    /**
     * Get desktopTraffic
     *
     * @return integer
     */
    public function getDesktopTraffic()
    {
        return $this->desktopTraffic;
    }

}
