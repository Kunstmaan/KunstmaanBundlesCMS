<?php

namespace Kunstmaan\MenuBundle\Tests\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\MenuBundle\Entity\BaseMenuItem;
use Kunstmaan\MenuBundle\Entity\Menu;
use Kunstmaan\MenuBundle\Entity\MenuItem;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

class MenuItemTest extends PHPUnit_Framework_TestCase
{
    public function testGetSetParent()
    {
        $son = new MenuItem();
        $daddy = new MenuItem();

        $son->setParent($daddy);
        $this->assertInstanceOf(MenuItem::class, $son->getParent());
    }

    public function testGetSetChildren()
    {
        $daddy = new MenuItem();
        $son = new MenuItem();
        $daughter = new MenuItem();

        $daddy->setChildren(new ArrayCollection([$son, $daughter]));
        $this->assertInstanceOf(ArrayCollection::class, $son->getChildren());
    }

    public function testBaseClassMethods()
    {
        $item = new MenuItem();
        $menu = new Menu();
        $nodeTranslation = new NodeTranslation();

        $item->setMenu($menu);
        $item->setType('Rubber Chicken');
        $item->setNodeTranslation($nodeTranslation);
        $item->setTitle('Buy Bitcoin Cash!');
        $item->setUrl('https://nasa.gov');
        $item->setNewWindow(true);
        $item->setLft(5);
        $item->setLvl(6);
        $item->setRgt(7);

        $this->assertInstanceOf(Menu::class, $item->getMenu());
        $this->assertEquals('Rubber Chicken', $item->getType());
        $this->assertInstanceOf(NodeTranslation::class, $item->getNodeTranslation());
        $this->assertEquals('Buy Bitcoin Cash!', $item->getTitle());
        $this->assertEquals('https://nasa.gov', $item->getUrl());
        $this->assertTrue($item->isNewWindow());
        $this->assertEquals(5, $item->getLft());
        $this->assertEquals(6, $item->getLvl());
        $this->assertEquals(7, $item->getRgt());
    }

    public function testGetDisplayUrl()
    {
        $item = new MenuItem();
        $nodeTranslation = new NodeTranslation();

        $nodeTranslation->setUrl('https://nasa.gov');
        $item->setType(BaseMenuItem::TYPE_PAGE_LINK);
        $item->setUrl('https://bitcoinwisdom.com');
        $item->setNodeTranslation($nodeTranslation);

        $this->assertEquals('https://nasa.gov', $item->getDisplayUrl());

        $item->setType(BaseMenuItem::TYPE_URL_LINK);
        $this->assertEquals('https://bitcoinwisdom.com', $item->getDisplayUrl());
    }

    public function testGetDisplayTitle()
    {
        $item = new MenuItem();
        $nodeTranslation = new NodeTranslation();

        $nodeTranslation->setTitle('Node');
        $item->setType(BaseMenuItem::TYPE_PAGE_LINK);
        $item->setNodeTranslation($nodeTranslation);
        $this->assertEquals('Node', $item->getDisplayTitle());

        $item->setTitle('Hello');
        $this->assertEquals('Hello', $item->getDisplayTitle());

        $item->setType(BaseMenuItem::TYPE_URL_LINK);
        $this->assertEquals('Hello', $item->getDisplayTitle());
    }

    public function testIsOnline()
    {
        $item = new MenuItem();
        $nodeTranslation = new NodeTranslation();
        $node = new Node();
        $node->setDeleted(false);
        $nodeTranslation->setOnline(false);
        $nodeTranslation->setNode($node);
        $item->setNodeTranslation($nodeTranslation);

        $this->assertFalse($item->isOnline());

        $item->setType(BaseMenuItem::TYPE_URL_LINK);
        $this->assertTrue($item->isOnline());

        $item->setType(BaseMenuItem::TYPE_PAGE_LINK);
        $this->assertFalse($item->isOnline());

        $nodeTranslation->setOnline(true);
        $this->assertTrue($item->isOnline());
    }

    public function testValidateEntity()
    {
        $item = new MenuItem();
        $item->setType(MenuItem::TYPE_PAGE_LINK);

        $violationBuilder = $this->getMockBuilder(ConstraintViolationBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $violationBuilder->expects($this->any())
            ->method('atPath')
            ->will($this->returnValue($violationBuilder));

        $violationBuilder->expects($this->any())
            ->method('addViolation')
            ->will($this->returnValue(true));

        $context = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->getMock();

        $context->expects($this->any())
        ->method('buildViolation')
        ->will($this->returnValue($violationBuilder));

        $item->validateEntity($context);
        $item->setType(MenuItem::TYPE_URL_LINK);
        $item->validateEntity($context);
    }
}
