<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\AdminPanel;

use Kunstmaan\AdminBundle\Helper\AdminPanel\AdminPanelAction;
use PHPUnit\Framework\TestCase;

class AdminPanelActionTest extends TestCase
{
    public function testGetSetRole()
    {
        $object = new AdminPanelAction(['http://fbi.gov'], 'FBI', 'fa-user', 'some.twig');
        $this->assertEquals('http://fbi.gov', $object->getUrl()[0]);
        $this->assertEquals('FBI', $object->getLabel());
        $this->assertEquals('fa-user', $object->getIcon());
        $this->assertEquals('some.twig', $object->getTemplate());
    }
}
