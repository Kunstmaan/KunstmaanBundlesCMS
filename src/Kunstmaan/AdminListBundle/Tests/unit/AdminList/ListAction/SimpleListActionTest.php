<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\ListAction;

use Kunstmaan\AdminListBundle\AdminList\ListAction\SimpleListAction;
use PHPUnit_Framework_TestCase;

/**
 * Class ListActionTest
 */
class SimpleListActionTest extends PHPUnit_Framework_TestCase
{
    public function test__construct()
    {
        $object = new SimpleListAction(['http://www.domain.com/action'], 'Label', 'icon.png', 'template.html.twig');

        $this->assertEquals('http://www.domain.com/action', $object->getUrl()[0]);
        $this->assertEquals('icon.png', $object->getIcon());
        $this->assertEquals('Label', $object->getLabel());
        $this->assertEquals('template.html.twig', $object->getTemplate());
    }
}
