<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\AfterXScrollPercentRule;
use Kunstmaan\LeadGenerationBundle\Form\Rule\AfterXScrollPercentRuleAdminType;
use PHPUnit\Framework\TestCase;

class AfterXScrollPercentRuleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $rule = new AfterXScrollPercentRule();
        $rule->setPercentage(10);

        $this->assertSame(10, $rule->getPercentage());
        $this->assertSame('AfterXScrollPercentRule', $rule->getJsObjectClass());
        $this->assertSame('/bundles/kunstmaanleadgeneration/js/rule/AfterXScrollPercentRule.js', $rule->getJsFilePath());
        $this->assertSame(AfterXScrollPercentRuleAdminType::class, $rule->getAdminType());
        $this->assertIsArray($rule->getJsProperties());
    }
}
