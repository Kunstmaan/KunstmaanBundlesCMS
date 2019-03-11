<?php

namespace Kunstmaan\NodeBundle\Tests\Entity;

use Codeception\Stub;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\AbstractStructurePage;
use Kunstmaan\NodeBundle\Form\PageAdminType;
use Kunstmaan\NodeBundle\Helper\RenderContext;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

class TestAbstractStructurePage extends AbstractStructurePage
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
 * Class AbstractStructurePageTest
 */
class AbstractStructurePageTest extends TestCase
{
    public function testIsAbstractStructurePage()
    {
        $structurePage = new TestAbstractStructurePage();
        $this->assertTrue($structurePage->isStructurePage());

        $node = new TestNode();
        $this->assertFalse($node->isStructurePage());
    }

    public function testIsOnline()
    {
        $structurePage = new TestAbstractStructurePage();
        $this->assertFalse($structurePage->isOnline());
    }

    public function testGetSetPageTitle()
    {
        $page = new TestAbstractStructurePage();
        $page->setTitle('The Title');
        $this->assertEquals('The Title', $page->getPageTitle());
        $this->assertEquals('The Title', $page->getTitle());
        $this->assertEquals('The Title', $page->__toString());
    }

    public function testGetSetParent()
    {
        /** @var HasNodeInterface $entity */
        $entity = Stub::makeEmpty(HasNodeInterface::class);
        $page = new TestAbstractStructurePage();
        $page->setParent($entity);
        $this->assertInstanceOf(get_class($entity), $page->getParent());
    }

    public function testGetDefaultAdminType()
    {
        $page = new TestAbstractStructurePage();
        $this->assertEquals(PageAdminType::class, $page->getDefaultAdminType());
    }

    public function testService()
    {
        // this method does nothing - is it required?
        $page = new TestAbstractStructurePage();
        $page->service(new Container(), new Request(), new RenderContext());
    }
}
