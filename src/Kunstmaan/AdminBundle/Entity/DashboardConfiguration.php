<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DashboardConfiguration
 *
 * @ORM\Entity
 * @ORM\Table(name="kuma_dashboard_configurations")
 */
class DashboardConfiguration extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     *
     * @return DashboardConfiguration
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return DashboardConfiguration
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }
}
