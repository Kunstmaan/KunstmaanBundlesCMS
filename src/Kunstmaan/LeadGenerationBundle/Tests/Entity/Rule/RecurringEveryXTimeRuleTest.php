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

        $this->assertSame(10, $rule->getDays());
        $this->assertSame(11, $rule->getHours());
        $this->assertSame(12, $rule->getMinutes());
        $this->assertSame(13, $rule->getTimes());
        $this->assertSame('RecurringEveryXTimeRule', $rule->getJsObjectClass());
        $this->assertSame('/bundles/kunstmaanleadgeneration/js/rule/RecurringEveryXTimeRule.js', $rule->getJsFilePath());
        $this->assertSame(RecurringEveryXTimeAdminType::class, $rule->getAdminType());
        $this->assertIsArray($rule->getJsProperties());
    }
}
