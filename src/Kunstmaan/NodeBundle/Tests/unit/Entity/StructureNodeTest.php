<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use Codeception\Stub;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\StructureNode;
use Kunstmaan\NodeBundle\Form\PageAdminType;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use PHPUnit_Framework_TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class TestStructureNode extends StructureNode
{
    public function getPossibleChildTypes()
    {
        return [];
    }
}

class TestNode extends AbstractPage
{
    public function getPossibleChildTypes()
    {
        return [];
    }
}

/**
 * Class StructureNodeTest
 */
class StructureNodeTest extends PHPUnit_Framework_TestCase
{
    public function testIsStructureNode()
    {
        $structureNode = new TestStructureNode();
        $this->assertTrue($structureNode->isStructureNode());

        $node = new TestNode();
        $this->assertFalse($node->isStructureNode());
    }

    public function testIsOnline()
    {
        $structureNode = new TestStructureNode();
        $this->assertFalse($structureNode->isOnline());
    }

    public function testGetSetPageTitle()
    {
        $node = new TestStructureNode();
        $node->setTitle('The Title');
        $this->assertEquals('The Title', $node->getPageTitle());
        $this->assertEquals('The Title', $node->getTitle());
        $this->assertEquals('The Title', $node->__toString());
    }

    public function testGetSetParent()
    {
        /** @var HasNodeInterface $entity */
        $entity = Stub::makeEmpty(HasNodeInterface::class);
        $node = new TestStructureNode();
        $node->setParent($entity);
        $this->assertInstanceOf(get_class($entity), $node->getParent());
    }

    public function testGetDefaultAdminType()
    {
        $node = new TestStructureNode();
        $this->assertEquals(PageAdminType::class, $node->getDefaultAdminType());
    }

    public function testService()
    {
        // this method does nothing - is it required?
        $node = new TestStructureNode();
        $node->service(new Container(), new Request(), new RenderContext());
    }
}
