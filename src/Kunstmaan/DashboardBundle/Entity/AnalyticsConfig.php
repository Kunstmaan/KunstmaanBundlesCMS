<?php

namespace Kunstmaan\DashboardBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AnalyticsToken
 *
 * @ORM\Table(name="kuma_analytics_config")
 * @ORM\Entity(repositoryClass="Kunstmaan\DashboardBundle\Repository\AnalyticsConfigRepository")
 */
class AnalyticsConfig extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{

    /**
     * @ORM\OneToMany(targetEntity="AnalyticsOverview", mappedBy="config", cascade={"persist"})
     */
    private $overviews;

    /**
     * Set overviews
     *
     * @param array $overviews
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
     * @var DateTime
     *
     * @ORM\Column(name="last_update", type="datetime", nullable=true)
     */
    private $lastUpdate = null;



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
     * @return AnalyticsToken
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
     * @return AnalyticsProperty
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
     * @return AnalyticsProperty
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
     * @return AnalyticsProperty
     */
    public function setProfileId($profileId)
    {
        $this->profileId = $profileId;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set lastUpdate
     *
     * @param DateTime $lastUpdate
     *
     * @return AnalyticsProperty
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }
}
