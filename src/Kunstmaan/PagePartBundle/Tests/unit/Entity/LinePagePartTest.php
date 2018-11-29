<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\LinePagePart;
use PHPUnit_Framework_TestCase;

/**
 * Class LinePagePartTest
 */
class LinePagePartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var LinePagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new LinePagePart();
    }

    public function testToString()
    {
        $this->assertEquals('LinePagePart', $this->object->__toString());
    }

    public function testGetDefaultView()
    {
        $this->assertEquals('KunstmaanPagePartBundle:LinePagePart:view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\PagePartBundle\Form\LinePagePartAdminType', $this->object->getDefaultAdminType());
    }
}
