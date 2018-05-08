<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\LocaleBlacklistRule;
use Kunstmaan\LeadGenerationBundle\Form\Rule\LocaleBlackListAdminType;
use Kunstmaan\LeadGenerationBundle\Tests\Entity\Popup\Popup;
use PHPUnit_Framework_TestCase;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2012-09-04 at 16:54:04.
 */
class LocaleBlacklistRuleTest extends PHPUnit_Framework_TestCase
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
        $this->assertTrue(is_array($rule->getJsProperties()));
    }
}
