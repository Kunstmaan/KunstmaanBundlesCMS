<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\ListAction;

use Kunstmaan\AdminListBundle\AdminList\ListAction\SimpleListAction;
use PHPUnit\Framework\TestCase;

class SimpleListActionTest extends TestCase
{
    public function testConstruct()
    {
        $object = new SimpleListAction(['http://www.domain.com/action'], 'Label', 'icon.png', 'template.html.twig');

        $this->assertEquals('http://www.domain.com/action', $object->getUrl()[0]);
        $this->assertEquals('icon.png', $object->getIcon());
        $this->assertEquals('Label', $object->getLabel());
        $this->assertEquals('template.html.twig', $object->getTemplate());
    }
}
