<?php

namespace Kunstmaan\LeadGenerationBundle\Tests\Entity\Popup;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\LocaleWhitelistRule;
use Kunstmaan\LeadGenerationBundle\Entity\Rule\UrlBlacklistRule;
use PHPUnit\Framework\TestCase;

class AbstractPopupTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $popup = new Popup();
        $rule = new LocaleWhitelistRule();
        $rule2 = new UrlBlacklistRule();
        $popup->setName('delboy1978uk');
        $popup->setId(256);
        $popup->setHtmlId(652);
        $popup->setRules(new ArrayCollection([$rule]));
        $popup->addRule($rule2);

        $this->assertInstanceOf(ArrayCollection::class, $popup->getRules());
        $this->assertSame(2, $popup->getRuleCount());
        $popup->removeRule($rule2);
        $this->assertSame(1, $popup->getRuleCount());
        $this->assertSame('delboy1978uk', $popup->getName());
        $this->assertSame(256, $popup->getId());
        $this->assertSame(652, $popup->getHtmlId());
        $this->assertSame(Popup::class, $popup->getFullClassname());
        $this->assertSame('Popup', $popup->getClassname());
        $this->assertNull($popup->getAvailableRules());
    }
}
