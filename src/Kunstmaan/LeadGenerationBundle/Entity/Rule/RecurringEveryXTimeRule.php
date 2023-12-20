<?php

namespace Kunstmaan\LeadGenerationBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Form\Rule\RecurringEveryXTimeAdminType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_rule_recurring_every_x_time")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_rule_recurring_every_x_time')]
class RecurringEveryXTimeRule extends AbstractRule
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(name: 'minutes', type: 'integer', nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    private $minutes;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(name: 'hours', type: 'integer', nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    private $hours;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(name: 'days', type: 'integer', nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    private $days;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    #[ORM\Column(name: 'times', type: 'integer', nullable: true)]
    #[Assert\GreaterThanOrEqual(0)]
    #[Assert\NotBlank]
    private $times;

    /**
     * @return int
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @param int $minutes
     *
     * @return RecurringEveryXTimeRule
     */
    public function setMinutes($minutes)
    {
        $this->minutes = $minutes;

        return $this;
    }

    /**
     * @return int
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param int $hours
     *
     * @return RecurringEveryXTimeRule
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param int $days
     *
     * @return RecurringEveryXTimeRule
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimes()
    {
        return $this->times;
    }

    /**
     * @param int $times
     *
     * @return RecurringEveryXTimeRule
     */
    public function setTimes($times)
    {
        $this->times = $times;

        return $this;
    }

    public function getJsObjectClass()
    {
        return 'RecurringEveryXTimeRule';
    }

    public function getJsProperties()
    {
        return [
            'minutes' => $this->getMinutes(),
            'hours' => $this->getHours(),
            'days' => $this->getDays(),
            'times' => $this->getTimes(),
        ];
    }

    public function getJsFilePath()
    {
        return '/bundles/kunstmaanleadgeneration/js/rule/RecurringEveryXTimeRule.js';
    }

    /**
     * @return string
     */
    public function getAdminType()
    {
        return RecurringEveryXTimeAdminType::class;
    }
}
