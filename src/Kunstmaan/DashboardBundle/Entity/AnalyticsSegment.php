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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="query", type="string", length=1000)
     */
    private $query;

    /**
     * @ORM\ManyToOne(targetEntity="AnalyticsConfig", inversedBy="segments")
     * @ORM\JoinColumn(name="config", referencedColumnName="id")
     */
    private $config;

    /**
     * @ORM\OneToMany(targetEntity="AnalyticsOverview", mappedBy="segment", cascade={"persist", "remove"})
     */
    private $overviews;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return AnalyticsSegment
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
     * Set query
     *
     * @param string $query
     *
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
     * Set overviews
     *
     * @param array $overviews
     *
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
