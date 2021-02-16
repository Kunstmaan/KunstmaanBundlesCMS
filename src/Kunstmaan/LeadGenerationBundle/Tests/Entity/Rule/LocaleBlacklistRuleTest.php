<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\LocaleBlacklistRule;
use Kunstmaan\LeadGenerationBundle\Form\Rule\LocaleBlackListAdminType;
use Kunstmaan\LeadGenerationBundle\Tests\Entity\Popup\Popup;
use PHPUnit\Framework\TestCase;

class LocaleBlacklistRuleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $rule = new LocaleBlacklistRule();
        $rule->setPopup(new Popup());
        $rule->setLocale('en');

        $this->assertEquals('en', $rule->getLocale());
        $this->assertEquals('LocaleBlacklistRule', $rule->getJsObjectClass());
        $this->assertEquals('/bundles/kunstmaanleadgeneration/js/rule/LocaleBlacklistRule.js', $rule->getJsFilePath());
        $this->assertEquals(LocaleBlackListAdminType::class, $rule->getAdminType());
        $this->assertEquals('kunstmaan_lead_generation.rule.service.localeruleservice', $rule->getService());
        $this->assertInstanceOf(Popup::class, $rule->getPopup());
        $this->assertIsArray($rule->getJsProperties());
    }
}
