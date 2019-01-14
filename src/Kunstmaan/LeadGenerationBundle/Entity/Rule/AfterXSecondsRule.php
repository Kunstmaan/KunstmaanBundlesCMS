<?php

namespace Kunstmaan\LeadGenerationBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Form\Rule\AfterXSecondsAdminType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_rule_after_x_seconds")
 */
class AfterXSecondsRule extends AbstractRule
{
    /**
     * @var int
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(0)
     */
    private $seconds;

    /**
     * @return int
     */
    public function getSeconds()
    {
        return $this->seconds;
    }

    /**
     * @param int $seconds
     *
     * @return AfterXSecondsRule
     */
    public function setSeconds($seconds)
    {
        $this->seconds = $seconds;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getJsObjectClass()
    {
        return 'AfterXSecondsRule';
    }

    /**
     * {@inheritdoc}
     */
    public function getJsProperties()
    {
        return array(
            'seconds' => $this->getSeconds(),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getJsFilePath()
    {
        return '/bundles/kunstmaanleadgeneration/js/rule/AfterXSecondsRule.js';
    }

    /**
     * @return string
     */
    public function getAdminType()
    {
        return AfterXSecondsAdminType::class;
    }
}
