<?php

namespace Kunstmaan\LeadGenerationBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Form\Rule\MaxXTimeAdminType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_rule_max_x_times")
 */
class MaxXTimesRule extends AbstractRule
{
    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     */
    private $times;

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
     * @return MaxXTimesRule
     */
    public function setTimes($times)
    {
        $this->times = $times;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsObjectClass()
    {
        return 'MaxXTimesRule';
    }

    /**
     * {@inheritdoc}
     */
    public function getJsProperties()
    {
        return [
            'times' => $this->getTimes(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getJsFilePath()
    {
        return '/bundles/kunstmaanleadgeneration/js/rule/MaxXTimesRule.js';
    }

    /**
     * @return string
     */
    public function getAdminType()
    {
        return MaxXTimeAdminType::class;
    }
}
