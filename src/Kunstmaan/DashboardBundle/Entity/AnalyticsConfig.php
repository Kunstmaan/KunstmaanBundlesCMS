<?php

namespace Kunstmaan\DashboardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;

/**
 * @ORM\Table(name="kuma_analytics_config")
 * @ORM\Entity(repositoryClass="Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository")
 */
class AnalyticsConfig extends AbstractEntity
{
    /**
     * @ORM\OneToMany(targetEntity="AnalyticsOverview", mappedBy="config", cascade={"persist", "remove"})
     */
    private $overviews;

    /**
     * @ORM\OneToMany(targetEntity="AnalyticsSegment", mappedBy="config", cascade={"persist", "remove"})
     */
    private $segments;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=true)
     */
    private $name = null;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="text", nullable=true)
     */
    private $token = null;

    /**
     * @var string
     *
     * @ORM\Column(name="account_id", type="string", nullable=true)
     */
    private $accountId = null;

    /**
     * @var string
     *
     * @ORM\Column(name="property_id", type="string", nullable=true)
     */
    private $propertyId = null;

    /**
     * @var string
     *
     * @ORM\Column(name="profile_id", type="string", nullable=true)
     */
    private $profileId = null;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    private $lastUpdate = null;

    /**
     * @var bool
     *
     * @ORM\Column(name="disable_goals", type="boolean")
     */
    private $disableGoals = false;

    /**
     * Set overviews
     *
     * @param array $overviews
     *
     * @return AnalyticsDailyOverviews
     */
    public function setOverviews($overviews)
    {
        $this->overviews = $overviews;

        return $this;
    }

    /**
     * Get overviews
     *
     * @return array
     */
    public function getOverviews()
    {
        return $this->overviews;
    }

    /**
     * Set segments
     *
     * @param array $segments
     *
     * @return AnalyticsDailysegments
     */
    public function setSegments($segments)
    {
        $this->segments = $segments;

        return $this;
    }

    /**
     * Get segments
     *
     * @return array
     */
    public function getSegments()
    {
        return $this->segments;
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
     * Set name
     *
     * @param string $name
     *
     * @return Analyticsname
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get propertyId
     *
     * @return string
     */
    public function getPropertyId()
    {
        return $this->propertyId;
    }

    /**
     * Set propertyId
     *
     * @param string $propertyId
     *
     * @return $this
     */
    public function setPropertyId($propertyId)
    {
        $this->propertyId = $propertyId;

        return $this;
    }

    /**
     * Get accountId
     *
     * @return string
     */
    public function getAccountId()
    {
        return $this->accountId;
    }

    /**
     * Set accountId
     *
     * @param string $accountId
     *
     * @return $this
     */
    public function setAccountId($accountId)
    {
        $this->accountId = $accountId;

        return $this;
    }

    /**
     * Get profileId
     *
     * @return string
     */
    public function getProfileId()
    {
        return $this->profileId;
    }

    /**
     * Set profileId
     *
     * @param string $profileId
     *
     * @return $this
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return \DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set lastUpdate
     *
     * @param \DateTime $lastUpdate
     *
     * @return $this
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get disableGoals
     *
     * @return bool
     */
    public function getDisableGoals()
    {
        return $this->disableGoals;
    }

    /**
     * Set disableGoals
     *
     * @param bool $disableGoals
     *
     * @return $this
     */
    public function setDisableGoals($disableGoals)
    {
        $this->disableGoals = $disableGoals;

        return $this;
    }
}
