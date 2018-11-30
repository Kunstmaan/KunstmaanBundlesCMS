<?php

namespace Kunstmaan\PagePartBundle\Tests\Entity;

use Kunstmaan\PagePartBundle\Entity\ToTopPagePart;
use PHPUnit_Framework_TestCase;

/**
 * Class ToTopPagePartTest
 */
class ToTopPagePartTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ToTopPagePart
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new ToTopPagePart();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Generated from @assert () == 'ToTopPagePart'.
     */
    public function testToString()
    {
        $this->assertEquals('ToTopPagePart', $this->object->__toString());
    }

    /**
     * Generated from @assert () == 'KunstmaanPagePartBundle:ToTopPagePart:view.html.twig'.
     */
    public function testGetDefaultView()
    {
        $this->assertEquals('KunstmaanPagePartBundle:ToTopPagePart:view.html.twig', $this->object->getDefaultView());
    }

    public function testGetDefaultAdminType()
    {
        $this->assertEquals('Kunstmaan\PagePartBundle\Form\ToTopPagePartAdminType', $this->object->getDefaultAdminType());
    }
}
