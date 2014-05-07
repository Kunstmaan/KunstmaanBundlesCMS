<?php

namespace Kunstmaan\DashboardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnalyticsSegment
 *
 * @ORM\Table(name="kuma_analytics_segment")
 * @ORM\Entity(repositoryClass="Kunstmaan\DashboardBundle\Repository\AnalyticsSegmentRepository")
 */
class AnalyticsSegment extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="query", type="string", length=255)
     */
    private $query;


    /**
     * Set query
     *
     * @param string $query
     * @return AnalyticsSegment
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Get query
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AnalyticsConfig", inversedBy="segements")
     * @ORM\JoinColumn(name="config_id", referencedColumnName="id")
     */
    private $config;

    /**
     * Get config
     *
     * @return integer
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set config
     *
     * @param integer $config
     *
     * @return AnalyticsTopReferrals
     */
    public function setConfig($config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @ORM\OneToMany(targetEntity="AnalyticsOverview", mappedBy="segment", cascade={"persist"})
     */
    private $overviews;

    /**
     * Set overviews
     *
     * @param array $overviews
     * @return $this
     */
    public function setoverviews($overviews)
    {
        $this->overviews = $overviews;

        return $this;
    }

    /**
     * Get overviews
     *
     * @return AnalyticsGoal[]
     */
    public function getoverviews()
    {
        return $this->overviews;
    }

}
