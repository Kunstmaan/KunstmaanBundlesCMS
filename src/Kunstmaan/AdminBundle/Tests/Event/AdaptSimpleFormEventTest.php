<?php

namespace Kunstmaan\AdminBundle\Tests\Event;

use Kunstmaan\AdminBundle\Event\AdaptSimpleFormEvent;
use Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs\TabPane;
use Kunstmaan\FormBundle\Entity\PageParts\EmailPagePart;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AdaptSimpleFormEventTest extends TestCase
{
    /**
     * @var AdaptSimpleFormEvent
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new AdaptSimpleFormEvent(new Request(), EmailPagePart::class, ['data' => 123], ['data2' => 123]);
    }

    public function testGetSet()
    {
        /** @var TabPane $tabPane */
        $tabPane = $this->createMock(TabPane::class);
        $this->object->setTabPane($tabPane);
        $this->assertInstanceOf(TabPane::class, $this->object->getTabPane());

        $this->assertArrayHasKey('data', $this->object->getData());
        $this->object->setData([]);
        $this->assertEmpty($this->object->getData());

        $this->assertArrayHasKey('data2', $this->object->getOptions());
        $this->object->setOptions([]);
        $this->assertEmpty($this->object->getOptions());

        $this->assertEquals(EmailPagePart::class, $this->object->getFormType());
        $this->object->setFormType('randomFormType');
        $this->assertEquals('randomFormType', $this->object->getFormType());

        $this->object->setRequest(new Request());
        $this->assertInstanceOf(Request::class, $this->object->getRequest());
    }
}
