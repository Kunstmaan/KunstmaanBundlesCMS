<?php

namespace Kunstmaan\DashboardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

/**
 * AnalyticsGoal
 *
 * @ORM\Table(name="kuma_analytics_goal")
 * @ORM\Entity(repositoryClass="Kunstmaan\DashboardBundle\Repository\AnalyticsGoalRepository")
 */
class AnalyticsGoal extends AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="AnalyticsOverview", inversedBy="goals")
     * @ORM\JoinColumn(name="overview_id", referencedColumnName="id")
     */
    private $overview;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer")
     */
    private $position;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="visits", type="integer")
     */
    private $visits;

    /**
     * @var array
     *
     * @ORM\Column(name="chart_data", type="text")
     */
    private $chartData = '';

    /**
     * Get overview
     *
     * @return int
     */
    public function getOverview()
    {
        return $this->overview;
    }

    /**
     * Set overview
     *
     * @param int $overview
     *
     * @return $this
     */
    public function setOverview($overview)
    {
        $this->overview = $overview;

        return $this;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return AnalyticsGoal
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return AnalyticsGoal
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set visits
     *
     * @param int $visits
     *
     * @return AnalyticsGoal
     */
    public function setVisits($visits)
    {
        $this->visits = $visits;

        return $this;
    }

    /**
     * Get visits
     *
     * @return int
     */
    public function getVisits()
    {
        return number_format($this->visits);
    }

    /**
     * Set chartData
     *
     * @param string $chartData
     *
     * @return AnalyticsGoal
     */
    public function setChartData($chartData)
    {
        $this->chartData = $chartData;

        return $this;
    }

    /**
     * Get chartData
     *
     * @return string
     */
    public function getChartData()
    {
        return $this->chartData;
    }
}
