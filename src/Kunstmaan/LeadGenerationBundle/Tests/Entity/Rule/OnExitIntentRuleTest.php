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

        $this->assertEquals(13, $rule->getDelay());
        $this->assertEquals(2, $rule->getSensitivity());
        $this->assertEquals(3, $rule->getTimer());
        $this->assertEquals('OnExitIntentRule', $rule->getJsObjectClass());
        $this->assertEquals('/bundles/kunstmaanleadgeneration/js/rule/OnExitIntentRule.js', $rule->getJsFilePath());
        $this->assertEquals(OnExitIntentRule::class, $rule->getFullClassname());
        $this->assertEquals('OnExitIntentRule', $rule->getClassname());
        $this->assertEquals(OnExitIntentAdminType::class, $rule->getAdminType());
        $this->assertIsArray($rule->getJsProperties());
        $this->assertNull($rule->getService());
        $this->assertNull($rule->getId());
    }
}
