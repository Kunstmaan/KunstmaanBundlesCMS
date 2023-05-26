<?php

namespace Kunstmaan\LeadGenerationBundle\Entity\Rule;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\LeadGenerationBundle\Form\Rule\AfterXScrollPercentRuleAdminType;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="kuma_rule_after_x_scroll_percent")
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_rule_after_x_scroll_percent')]
class AfterXScrollPercentRule extends AbstractRule
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min = 0, max = 100)
     */
    #[ORM\Column(name: 'percentage', type: 'integer')]
    private $percentage;

    /**
     * @return int
     */
    public function getPercentage()
    {
        return $this->percentage;
    }

    /**
     * @param int $percentage
     *
     * @return self
     */
    public function setPercentage($percentage)
    {
        $this->percentage = $percentage;

        return $this;
    }

    public function getJsObjectClass()
    {
        return 'AfterXScrollPercentRule';
    }

    public function getJsProperties()
    {
        return [
            'percentage' => $this->getPercentage(),
        ];
    }

    public function getJsFilePath()
    {
        return '/bundles/kunstmaanleadgeneration/js/rule/AfterXScrollPercentRule.js';
    }

    /**
     * @return string
     */
    public function getAdminType()
    {
        return AfterXScrollPercentRuleAdminType::class;
    }
}
