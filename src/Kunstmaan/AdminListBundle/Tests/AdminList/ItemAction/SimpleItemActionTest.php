<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use PHPUnit\Framework\TestCase;

class SimpleItemActionTest extends TestCase
{
    public function testConstruct()
    {
        $object = new SimpleItemAction(fn($item) => 'http://www.domain.com/action', 'icon.png', 'Label', 'template.html.twig');

        $item = new \stdClass();
        $this->assertSame('http://www.domain.com/action', $object->getUrlFor($item));
        $this->assertSame('icon.png', $object->getIconFor($item));
        $this->assertSame('Label', $object->getLabelFor($item));
        $this->assertSame('template.html.twig', $object->getTemplate());

        $object = new SimpleItemAction('not callable', 'icon.png', 'Label', 'template.html.twig');
        $this->assertNull($object->getUrlFor($item));
    }
}
