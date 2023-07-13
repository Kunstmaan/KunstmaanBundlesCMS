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

        $this->assertSame(10, $rule->getSeconds());
        $this->assertSame('AfterXSecondsRule', $rule->getJsObjectClass());
        $this->assertSame('/bundles/kunstmaanleadgeneration/js/rule/AfterXSecondsRule.js', $rule->getJsFilePath());
        $this->assertSame(AfterXSecondsAdminType::class, $rule->getAdminType());
        $this->assertIsArray($rule->getJsProperties());
    }
}
