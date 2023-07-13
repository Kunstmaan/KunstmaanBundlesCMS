<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\ListAction;

use Kunstmaan\AdminListBundle\AdminList\ListAction\SimpleListAction;
use PHPUnit\Framework\TestCase;

class SimpleListActionTest extends TestCase
{
    public function testConstruct()
    {
        $object = new SimpleListAction(['http://www.domain.com/action'], 'Label', 'icon.png', 'template.html.twig');

        $this->assertSame('http://www.domain.com/action', $object->getUrl()[0]);
        $this->assertSame('icon.png', $object->getIcon());
        $this->assertSame('Label', $object->getLabel());
        $this->assertSame('template.html.twig', $object->getTemplate());
    }
}
