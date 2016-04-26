<?php

namespace Kunstmaan\SeoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Entity\AbstractEntity;
use Kunstmaan\SeoBundle\Form\RobotsType;

/**
 * Robots.txt data
 * @ORM\Entity
 * @ORM\Table(name="kuma_robots")
 */
class Robots extends AbstractEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="robots_txt", type="text", nullable=true)
     */
    protected $robotsTxt;

    /**
     * @return string
     */
    public function getRobotsTxt()
    {
        return $this->robotsTxt;
    }

    /**
     * @param string $robotsTxt
     */
    public function setRobotsTxt($robotsTxt)
    {
        $this->robotsTxt = $robotsTxt;
    }

    /**
     * @return RobotsType
     */
    public function getDefaultAdminType()
    {
        return new RobotsType();
    }

    /**
     * Return string representation of entity
     *
     * @return string
     */
    public function __toString()
    {
        return "Robots";
    }

}
