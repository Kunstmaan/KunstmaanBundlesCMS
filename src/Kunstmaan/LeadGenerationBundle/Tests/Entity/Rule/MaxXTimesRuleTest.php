<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\MaxXTimesRule;
use Kunstmaan\LeadGenerationBundle\Form\Rule\MaxXTimeAdminType;
use PHPUnit\Framework\TestCase;

class MaxXTimesRuleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $rule = new MaxXTimesRule();
        $rule->setTimes(13);

        $this->assertSame(13, $rule->getTimes());
        $this->assertSame('MaxXTimesRule', $rule->getJsObjectClass());
        $this->assertSame('/bundles/kunstmaanleadgeneration/js/rule/MaxXTimesRule.js', $rule->getJsFilePath());
        $this->assertSame(MaxXTimeAdminType::class, $rule->getAdminType());
        $this->assertIsArray($rule->getJsProperties());
    }
}
