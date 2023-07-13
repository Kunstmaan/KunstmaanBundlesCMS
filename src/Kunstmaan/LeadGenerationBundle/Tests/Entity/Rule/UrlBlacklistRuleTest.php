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

        $this->assertSame('https://nasa.gov', $rule->getUrls());
        $this->assertSame('UrlBlacklistRule', $rule->getJsObjectClass());
        $this->assertSame('/bundles/kunstmaanleadgeneration/js/rule/UrlBlacklistRule.js', $rule->getJsFilePath());
        $this->assertSame(UrlBlackListAdminType::class, $rule->getAdminType());
        $this->assertInstanceOf(Popup::class, $rule->getPopup());
        $this->assertIsArray($rule->getJsProperties());
    }
}
