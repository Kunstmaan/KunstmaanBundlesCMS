<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\LocaleWhitelistRule;
use Kunstmaan\LeadGenerationBundle\Form\Rule\LocaleWhiteListAdminType;
use Kunstmaan\LeadGenerationBundle\Tests\Entity\Popup\Popup;
use PHPUnit\Framework\TestCase;

class LocaleWhitelistRuleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $rule = new LocaleWhitelistRule();
        $rule->setPopup(new Popup());
        $rule->setLocale('en');

        $this->assertSame('en', $rule->getLocale());
        $this->assertSame('LocaleWhitelistRule', $rule->getJsObjectClass());
        $this->assertSame('/bundles/kunstmaanleadgeneration/js/rule/LocaleWhitelistRule.js', $rule->getJsFilePath());
        $this->assertSame(LocaleWhiteListAdminType::class, $rule->getAdminType());
        $this->assertSame('kunstmaan_lead_generation.rule.service.localeruleservice', $rule->getService());
        $this->assertInstanceOf(Popup::class, $rule->getPopup());
        $this->assertIsArray($rule->getJsProperties());
    }
}
