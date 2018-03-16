<?php

namespace Kunstmaan\DashboardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

/**
 * AnalyticsOverview
 *
 * @ORM\Table(name="kuma_analytics_overview")
 * @ORM\Entity(repositoryClass="Kunstmaan\DashboardBundle\Repository\AnalyticsOverviewRepository")
 */
class AnalyticsOverview extends AbstractEntity
{
    /**
     * @var AnalyticsConfig
     *
     * @ORM\ManyToOne(targetEntity="AnalyticsConfig", inversedBy="overviews")
     * @ORM\JoinColumn(name="config_id", referencedColumnName="id")
     */
    private $config;

    /**
     * @var AnalyticsSegment
     *
     * @ORM\ManyToOne(targetEntity="AnalyticsSegment", inversedBy="overviews")
     * @ORM\JoinColumn(name="segment_id", referencedColumnName="id", nullable=true)
     */
    private $segment;

    /**
     * @ORM\OneToMany(targetEntity="AnalyticsGoal", mappedBy="overview", cascade={"persist", "remove"})
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $goals;

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
     * @var boolean
     *
     * @ORM\Column(name="use_year", type="boolean")
     */
    private $useYear = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="sessions", type="integer")
     */
    private $sessions = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="users", type="integer")
     */
    private $users = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="returning_users", type="integer")
     */
    private $returningUsers = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="new_users", type="float")
     */
    private $newUsers = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pageviews", type="integer")
     */
    private $pageViews = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="pages_per_session", type="float")
     */
    private $pagesPerSession = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="chart_data_max_value", type="integer")
     */
    private $chartDataMaxValue = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="avg_session_duration", type="string")
     */
    private $avgSessionDuration = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="chart_data", type="text")
     */
    private $chartData = '';

    /**
     * Get percentage of returning users
     *
     * @return int
     */
    public function getReturningUsersPercentage()
    {
        return $this->returningUsers ? round(($this->returningUsers / $this->sessions) * 100) : 0;
    }

    /**
     * Get percentage of new users
     *
     * @return int
     */
    public function getNewUsersPercentage()
    {
        return $this->newUsers ? round(($this->newUsers / $this->sessions) * 100) : 0;
    }

    /**
     * @return AnalyticsConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param AnalyticsConfig $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * @return AnalyticsSegment
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * @param AnalyticsSegment $segment
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;
    }

    /**
     * Set goals
     *
     * @param array $goals
     *
     * @return $this
     */
    public function setGoals($goals)
    {
        $this->goals = $goals;

        return $this;
    }

    /**
     * Get goals
     *
     * @return AnalyticsGoal[]
     */
    public function getGoals()
    {
        return $this->goals;
    }

    /**
     * @return array
     */
    public function getActiveGoals()
    {
        $goals = [];
        foreach ($this->getGoals() as $goal) {
            if ($goal->getVisits()) {
                $goals[] = $goal;
            }
        }

        return $goals;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getTimespan()
    {
        return $this->timespan;
    }

    /**
     * @param int $timespan
     */
    public function setTimespan($timespan)
    {
        $this->timespan = $timespan;
    }

    /**
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * @param int $startOffset
     */
    public function setStartOffset($startOffset)
    {
        $this->startOffset = $startOffset;
    }

    /**
     * @return bool
     */
    public function getUseYear()
    {
        return $this->useYear;
    }

    /**
     * @param bool $useYear
     */
    public function setUseYear($useYear)
    {
        $this->useYear = $useYear;
    }

    /**
     * @return int
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * @param int $sessions
     */
    public function setSessions($sessions)
    {
        $this->sessions = $sessions;
    }

    /**
     * @return int
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param int $users
     */
    public function setUsers($users)
    {
        $this->users = $users;
    }

    /**
     * @return int
     */
    public function getReturningUsers()
    {
        return $this->returningUsers;
    }

    /**
     * @param int $returningUsers
     */
    public function setReturningUsers($returningUsers)
    {
        $this->returningUsers = $returningUsers;
    }

    /**
     * @return float
     */
    public function getNewUsers()
    {
        return $this->newUsers;
    }

    /**
     * @param float $newUsers
     */
    public function setNewUsers($newUsers)
    {
        $this->newUsers = $newUsers;
    }

    /**
     * @return int
     */
    public function getPageViews()
    {
        return $this->pageViews;
    }

    /**
     * @param int $pageViews
     */
    public function setPageViews($pageViews)
    {
        $this->pageViews = $pageViews;
    }

    /**
     * @return float
     */
    public function getPagesPerSession()
    {
        return $this->pagesPerSession;
    }

    /**
     * @param float $pagesPerSession
     */
    public function setPagesPerSession($pagesPerSession)
    {
        $this->pagesPerSession = $pagesPerSession;
    }

    /**
     * @return int
     */
    public function getChartDataMaxValue()
    {
        return $this->chartDataMaxValue;
    }

    /**
     * @param int $chartDataMaxValue
     */
    public function setChartDataMaxValue($chartDataMaxValue)
    {
        $this->chartDataMaxValue = $chartDataMaxValue;
    }

    /**
     * @return string
     */
    public function getAvgSessionDuration()
    {
        return $this->avgSessionDuration;
    }

    /**
     * @param string $avgSessionDuration
     */
    public function setAvgSessionDuration($avgSessionDuration)
    {
        $this->avgSessionDuration = $avgSessionDuration;
    }

    /**
     * @return string
     */
    public function getChartData()
    {
        return $this->chartData;
    }

    /**
     * @param string $chartData
     */
    public function setChartData($chartData)
    {
        $this->chartData = $chartData;
    }
}
