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

        $this->assertSame('en', $rule->getLocale());
        $this->assertSame('LocaleBlacklistRule', $rule->getJsObjectClass());
        $this->assertSame('/bundles/kunstmaanleadgeneration/js/rule/LocaleBlacklistRule.js', $rule->getJsFilePath());
        $this->assertSame(LocaleBlackListAdminType::class, $rule->getAdminType());
        $this->assertSame('kunstmaan_lead_generation.rule.service.localeruleservice', $rule->getService());
        $this->assertInstanceOf(Popup::class, $rule->getPopup());
        $this->assertIsArray($rule->getJsProperties());
    }
}
