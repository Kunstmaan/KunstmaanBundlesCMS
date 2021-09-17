<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Rule;

use Kunstmaan\LeadGenerationBundle\Entity\Rule\UrlBlacklistRule;
use Kunstmaan\LeadGenerationBundle\Form\Rule\UrlBlackListAdminType;
use Kunstmaan\LeadGenerationBundle\Tests\Entity\Popup\Popup;
use PHPUnit\Framework\TestCase;

class UrlBlacklistRuleTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $rule = new UrlBlacklistRule();
        $rule->setPopup(new Popup());
        $rule->setUrls('https://nasa.gov');

        $this->assertEquals('https://nasa.gov', $rule->getUrls());
        $this->assertEquals('UrlBlacklistRule', $rule->getJsObjectClass());
        $this->assertEquals('/bundles/kunstmaanleadgeneration/js/rule/UrlBlacklistRule.js', $rule->getJsFilePath());
        $this->assertEquals(UrlBlackListAdminType::class, $rule->getAdminType());
        $this->assertInstanceOf(Popup::class, $rule->getPopup());
        $this->assertIsArray($rule->getJsProperties());
    }
}
