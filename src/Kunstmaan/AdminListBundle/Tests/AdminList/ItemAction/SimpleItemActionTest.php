<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\ItemAction;

use Kunstmaan\AdminListBundle\AdminList\ItemAction\SimpleItemAction;
use PHPUnit\Framework\TestCase;
use stdClass;

class SimpleItemActionTest extends TestCase
{
    public function testConstruct()
    {
        $object = new SimpleItemAction(function ($item) {
            return 'http://www.domain.com/action';
        }, 'icon.png', 'Label', 'template.html.twig');

        $item = new stdClass();
        $this->assertEquals('http://www.domain.com/action', $object->getUrlFor($item));
        $this->assertEquals('icon.png', $object->getIconFor($item));
        $this->assertEquals('Label', $object->getLabelFor($item));
        $this->assertEquals('template.html.twig', $object->getTemplate());

        $object = new SimpleItemAction('not callable', 'icon.png', 'Label', 'template.html.twig');
        $this->assertNull($object->getUrlFor($item));
    }
}
