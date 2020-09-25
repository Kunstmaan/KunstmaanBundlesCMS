<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\RecurringEveryXTimeRule;
use Kunstmaan\LeadGenerationBundle\Form\Rule\RecurringEveryXTimeAdminType;
use PHPUnit\Framework\TestCase;

class RecurringEveryXTimeRuleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $rule = new RecurringEveryXTimeRule();
        $rule->setDays(10);
        $rule->setHours(11);
        $rule->setMinutes(12);
        $rule->setTimes(13);

        $this->assertEquals(10, $rule->getDays());
        $this->assertEquals(11, $rule->getHours());
        $this->assertEquals(12, $rule->getMinutes());
        $this->assertEquals(13, $rule->getTimes());
        $this->assertEquals('RecurringEveryXTimeRule', $rule->getJsObjectClass());
        $this->assertEquals('/bundles/kunstmaanleadgeneration/js/rule/RecurringEveryXTimeRule.js', $rule->getJsFilePath());
        $this->assertEquals(RecurringEveryXTimeAdminType::class, $rule->getAdminType());
        $this->assertIsArray($rule->getJsProperties());
    }
}
