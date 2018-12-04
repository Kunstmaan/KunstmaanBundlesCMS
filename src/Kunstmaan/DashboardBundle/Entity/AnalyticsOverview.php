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
     * @ORM\ManyToOne(targetEntity="AnalyticsConfig", inversedBy="overviews")
     * @ORM\JoinColumn(name="config_id", referencedColumnName="id")
     */
    private $config;

    /**
     * @ORM\ManyToOne(targetEntity="AnalyticsSegment", inversedBy="overviews")
     * @ORM\JoinColumn(name="segment_id", referencedColumnName="id", nullable=true)
     */
    private $segment = null;

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
     * @var int
     *
     * @ORM\Column(name="timespan", type="integer")
     */
    private $timespan;

    /**
     * @var int
     *
     * @ORM\Column(name="start_days_ago", type="integer")
     */
    private $startOffset = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="use_year", type="boolean")
     */
    private $useYear = false;

    /**
     * @var int
     *
     * @ORM\Column(name="sessions", type="integer")
     */
    private $sessions = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="users", type="integer")
     */
    private $users = 0;

    /**
     * @var int
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
     * @var int
     *
     * @ORM\Column(name="pageviews", type="integer")
     */
    private $pageviews = 0;

    /**
     * @var float
     *
     * @ORM\Column(name="pages_per_session", type="float")
     */
    private $pagesPerSession = 0;

    /**
     * @var int
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
     * @var array
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
     * Get config
     *
     * @return int
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set config
     *
     * @param int $config
     *
     * @return AnalyticsTopReferrals
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get segment
     *
     * @return int
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * Set segment
     *
     * @param int $segment
     *
     * @return AnalyticsTopReferrals
     */
    public function setSegment($segment)
    {
        $this->segment = $segment;

        return $this;
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
        $goals = array();
        foreach ($this->getGoals() as $goal) {
            if ($goal->getVisits()) {
                $goals[] = $goal;
            }
        }

        return $goals;
    }

    /**
     * Set chartData
     *
     * @param array $chartData
     *
     * @return $this
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
     * Set newUsers
     *
     * @param float $newUsers
     *
     * @return AnalyticsOverview
     */
    public function setNewUsers($newUsers)
    {
        $this->newUsers = $newUsers;

        return $this;
    }

    /**
     * Get newUsers
     *
     * @return float
     */
    public function getNewUsers()
    {
        return $this->newUsers;
    }

    /**
     * @param int $returningUsers
     *
     * @return $this
     */
    public function setReturningUsers($returningUsers)
    {
        $this->returningUsers = $returningUsers;

        return $this;
    }

    /**
     * @return int
     */
    public function getReturningUsers()
    {
        return $this->returningUsers;
    }

    /**
     * Set title
     *
     * @param string $title
     *
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
     * @param int $startOffset
     *
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
     * @return int
     */
    public function getStartOffset()
    {
        return $this->startOffset;
    }

    /**
     * Set timespan
     *
     * @param int $timespan
     *
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
     * @return int
     */
    public function getTimespan()
    {
        return $this->timespan;
    }

    /**
     * Set sessions
     *
     * @param int $sessions
     *
     * @return AnalyticsOverview
     */
    public function setSessions($sessions)
    {
        $this->sessions = $sessions;

        return $this;
    }

    /**
     * Get sessions
     *
     * @return int
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Get Users
     *
     * @return int
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set users
     *
     * @param int $users
     *
     * @return AnalyticsOverview
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Set pageviews
     *
     * @param int $pageviews
     *
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
     * @return int
     */
    public function getPageviews()
    {
        return $this->pageviews;
    }

    /**
     * Set pagesPerSession
     *
     * @param float $pagesPerSession
     *
     * @return AnalyticsOverview
     */
    public function setPagesPerSession($pagesPerSession)
    {
        $this->pagesPerSession = $pagesPerSession;

        return $this;
    }

    /**
     * Get pagesPerSession
     *
     * @return float
     */
    public function getPagesPerSession()
    {
        return $this->pagesPerSession;
    }

    /**
     * Set avgSessionDuration
     *
     * @param int $avgSessionDuration
     *
     * @return AnalyticsOverview
     */
    public function setAvgSessionDuration($avgSessionDuration)
    {
        $this->avgSessionDuration = $avgSessionDuration;

        return $this;
    }

    /**
     * Get avgSessionDuration
     *
     * @return string
     */
    public function getAvgSessionDuration()
    {
        return $this->avgSessionDuration;
    }

    /**
     * Set useYear
     *
     * @param int $useYear
     *
     * @return AnalyticsOverview
     */
    public function setUseYear($useYear)
    {
        $this->useYear = $useYear;

        return $this;
    }

    /**
     * Get useYear
     *
     * @return int
     */
    public function getUseYear()
    {
        return $this->useYear;
    }

    /**
     * Set chartDataMaxValue
     *
     * @param int $chartDataMaxValue
     *
     * @return AnalyticsOverview
     */
    public function setChartDataMaxValue($chartDataMaxValue)
    {
        $this->chartDataMaxValue = $chartDataMaxValue;

        return $this;
    }

    /**
     * Get chartDataMaxValue
     *
     * @return int
     */
    public function getChartDataMaxValue()
    {
        return $this->chartDataMaxValue;
    }
}
