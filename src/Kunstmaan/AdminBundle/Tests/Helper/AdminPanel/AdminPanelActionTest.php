<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\AdminPanel;

use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAction;
use PHPUnit\Framework\TestCase;

class AdminPanelActionTest extends TestCase
{
    public function testGetSetRole()
    {
        $object = new AdminPanelAction(['http://fbi.gov'], 'FBI', 'fa-user', 'some.twig');
        $this->assertSame('http://fbi.gov', $object->getUrl()[0]);
        $this->assertSame('FBI', $object->getLabel());
        $this->assertSame('fa-user', $object->getIcon());
        $this->assertSame('some.twig', $object->getTemplate());
    }
}
