<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\OnExitIntentRule;
use Kunstmaan\LeadGenerationBundle\Form\Rule\OnExitIntentAdminType;
use PHPUnit\Framework\TestCase;

class OnExitIntentRuleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $rule = new OnExitIntentRule();
        $rule->setDelay(13);
        $rule->setSensitivity(2);
        $rule->setTimer(3);

        $this->assertSame(13, $rule->getDelay());
        $this->assertSame(2, $rule->getSensitivity());
        $this->assertSame(3, $rule->getTimer());
        $this->assertSame('OnExitIntentRule', $rule->getJsObjectClass());
        $this->assertSame('/bundles/kunstmaanleadgeneration/js/rule/OnExitIntentRule.js', $rule->getJsFilePath());
        $this->assertSame(OnExitIntentRule::class, $rule->getFullClassname());
        $this->assertSame('OnExitIntentRule', $rule->getClassname());
        $this->assertSame(OnExitIntentAdminType::class, $rule->getAdminType());
        $this->assertIsArray($rule->getJsProperties());
        $this->assertNull($rule->getService());
        $this->assertNull($rule->getId());
    }
}
