<?php

namespace Kunstmaan\DashboardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnalyticsGoal
 *
 * @ORM\Table(name="kuma_analytics_goal")
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\AnalyticsGoalRepository")
 */
class AnalyticsGoal extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="AnalyticsOverview", inversedBy="goals")
     * @ORM\JoinColumn(name="overview_id", referencedColumnName="id")
     */
    private $overview;

    /**
     * @var integer
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
     * @var integer
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
     * @return integer
     */
    public function getOverview()
    {
        return $this->overview;
    }

    /**
     * Set overview
     *
     * @param integer $overview
     *
     * @return AnalyticsTopReferrals
     */
    public function setOverview($overview)
    {
        $this->overview = $overview;

        return $this;
    }

    /**
     * Set position
     *
     * @param integer $position
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
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set name
     *
     * @param string $name
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
     * @param integer $visits
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
     * @return integer
     */
    public function getVisits()
    {
        return number_format($this->visits);
    }

    /**
     * Set chartData
     *
     * @param string $chartData
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
