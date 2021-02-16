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

        $this->assertEquals(13, $rule->getTimes());
        $this->assertEquals('MaxXTimesRule', $rule->getJsObjectClass());
        $this->assertEquals('/bundles/kunstmaanleadgeneration/js/rule/MaxXTimesRule.js', $rule->getJsFilePath());
        $this->assertEquals(MaxXTimeAdminType::class, $rule->getAdminType());
        $this->assertIsArray($rule->getJsProperties());
    }
}
