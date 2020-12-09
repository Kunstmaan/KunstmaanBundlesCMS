<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\AfterXSecondsRule;
use Kunstmaan\LeadGenerationBundle\Form\Rule\AfterXSecondsAdminType;
use PHPUnit\Framework\TestCase;

class AfterXSecondsRuleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $rule = new AfterXSecondsRule();
        $rule->setSeconds(10);

        $this->assertEquals(10, $rule->getSeconds());
        $this->assertEquals('AfterXSecondsRule', $rule->getJsObjectClass());
        $this->assertEquals('/bundles/kunstmaanleadgeneration/js/rule/AfterXSecondsRule.js', $rule->getJsFilePath());
        $this->assertEquals(AfterXSecondsAdminType::class, $rule->getAdminType());
        $this->assertIsArray($rule->getJsProperties());
    }
}
