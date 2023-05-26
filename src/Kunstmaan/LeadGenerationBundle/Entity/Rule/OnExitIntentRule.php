<?php

namespace Kunstmaan\LeadGenerationBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Form\Rule\OnExitIntentAdminType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_rule_on_exit_intent")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_rule_on_exit_intent')]
class OnExitIntentRule extends AbstractRule
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(0)
     */
    #[ORM\Column(name: 'sensitivity', type: 'integer', nullable: true)]
    private $sensitivity;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(0)
     */
    #[ORM\Column(name: 'timer', type: 'integer', nullable: true)]
    private $timer;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\GreaterThanOrEqual(0)
     */
    #[ORM\Column(name: 'delay', type: 'integer', nullable: true)]
    private $delay;

    /**
     * @return int
     */
    public function getSensitivity()
    {
        return $this->sensitivity;
    }

    /**
     * @param int $sensitivity
     *
     * @return OnExitIntentRule
     */
    public function setSensitivity($sensitivity)
    {
        $this->sensitivity = $sensitivity;

        return $this;
    }

    /**
     * @return int
     */
    public function getTimer()
    {
        return $this->timer;
    }

    /**
     * @param int $timer
     *
     * @return OnExitIntentRule
     */
    public function setTimer($timer)
    {
        $this->timer = $timer;

        return $this;
    }

    /**
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * @param int $delay
     *
     * @return OnExitIntentRule
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    public function getJsObjectClass()
    {
        return 'OnExitIntentRule';
    }

    public function getJsProperties()
    {
        return [
            'sensitivity' => $this->getSensitivity(),
            'timer' => $this->getTimer(),
            'delay' => $this->getDelay(),
        ];
    }

    public function getJsFilePath()
    {
        return '/bundles/kunstmaanleadgeneration/js/rule/OnExitIntentRule.js';
    }

    /**
     * @return string
     */
    public function getAdminType()
    {
        return OnExitIntentAdminType::class;
    }
}
