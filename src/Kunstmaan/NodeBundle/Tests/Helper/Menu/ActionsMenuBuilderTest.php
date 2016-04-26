<?php

namespace Kunstmaan\NodeBundle\Tests\Helper\Menu;

use Knp\Menu\MenuFactory;
use Knp\Menu\Integration\Symfony\RoutingExtension;
use Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder;
use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use Kunstmaan\NodeBundle\Tests\Stubs\TestRepository;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Entity\Node;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ActionsMenuBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ActionsMenuBuilder
     */
    protected $builder;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @covers Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder::__construct
     */
    protected function setUp()
    {
        /* @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->getMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $routingExtension = new RoutingExtension($urlGenerator);
        $factory = new MenuFactory();
        $factory->addExtension($routingExtension);
        $em = $this->getMockedEntityManager();
        /* @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->getMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        /* @var RouterInterface $router */
        $router = $this->getMock('Symfony\Component\Routing\RouterInterface');
        $authorizationChecker = $this->getMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $authorizationChecker->expects($this->any())
            ->method('isGranted')
            ->will($this->returnValue(true));

        $this->builder = new ActionsMenuBuilder($factory, $em, $router, $dispatcher, $authorizationChecker, new PagesConfiguration([]));
    }

    /**
     * https://gist.github.com/1331789
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getMockedEntityManager()
    {
        $emMock = $this->getMock('\Doctrine\ORM\EntityManager',
            array('getRepository', 'getClassMetadata', 'persist', 'flush'), array(), '', false);
        $emMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue(new TestRepository()));
        $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object)array('name' => 'aClass')));
        $emMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));

        return $emMock;  // it tooks 13 lines to achieve mock!
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder::createSubActionsMenu
     */
    public function testCreateSubActionsMenu()
    {
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setNode(new Node());

        $nodeVersion = new NodeVersion();
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);


        $menu = $this->builder->createSubActionsMenu();
        $this->assertNotNull($menu->getChild('subaction.versions'));

        $this->assertEquals('page-sub-actions', $menu->getChildrenAttribute('class'));
    }

    /**
     * @covers Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder::createActionsMenu
     */
    public function testCreateActionsMenuDraft()
    {
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setNode(new Node());

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType('draft');
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);


        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.saveasdraft'));
        $this->assertNull($menu->getChild('action.recopyfromlanguage'));
        $this->assertNotNull($menu->getChild('action.publish'));
        $this->assertNotNull($menu->getChild('action.preview'));
        $this->assertNull($menu->getChild('action.save'));
        $this->assertNull($menu->getChild('action.delete'));;

        $this->assertEquals('page-main-actions js-auto-collapse-buttons', $menu->getChildrenAttribute('class'));
    }

    /**
     * testCreateActionsMenuPublic
     */
    public function testCreateActionsMenuPublic()
    {
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setNode(new Node());

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType("public");
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);


        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.save'));
        $this->assertNotNull($menu->getChild('action.saveasdraft'));
        $this->assertNull($menu->getChild('action.recopyfromlanguage'));
        $this->assertNotNull($menu->getChild('action.preview'));
        $this->assertNotNull($menu->getChild('action.publish'));
        $this->assertNull($menu->getChild('action.unpublish'));
        $this->assertNull($menu->getChild('action.delete'));

        $nodeTranslation->setOnline(true);
        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.save'));
        $this->assertNotNull($menu->getChild('action.saveasdraft'));
        $this->assertNull($menu->getChild('action.recopyfromlanguage'));
        $this->assertNotNull($menu->getChild('action.preview'));
        $this->assertNull($menu->getChild('action.publish'));
        $this->assertNotNull($menu->getChild('action.unpublish'));
        $this->assertNull($menu->getChild('action.delete'));

        $this->assertEquals('page-main-actions js-auto-collapse-buttons', $menu->getChildrenAttribute('class'));
    }

    /**
     * testCreateActionsMenuNonEditable
     */
    public function testCreateActionsMenuNonEditable()
    {
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setNode(new Node());

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType("public");
        $nodeVersion->setNodeTranslation($nodeTranslation);
        $this->builder->setEditableNode(false);

        $this->builder->setActiveNodeVersion($nodeVersion);
        $nodeTranslation->setOnline(false);


        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.save')); // We want to save.
        $this->assertNull($menu->getChild('action.saveasdraft'));
        $this->assertNull($menu->getChild('action.recopyfromlanguage'));
        $this->assertNull($menu->getChild('action.preview'));
        $this->assertNull($menu->getChild('action.publish'));
        $this->assertNull($menu->getChild('action.unpublish'));

        $this->assertEquals('page-main-actions js-auto-collapse-buttons', $menu->getChildrenAttribute('class'));
    }

    /**
     * @covers Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder::createTopActionsMenu
     */
    public function testCreateTopActionsMenu()
    {
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setNode(new Node());

        $nodeVersion = new NodeVersion();
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);


        $menu = $this->builder->createTopActionsMenu();
        $this->assertEquals('page-main-actions page-main-actions--top', $menu->getChildrenAttribute('class'));
        $this->assertEquals('page-main-actions-top', $menu->getChildrenAttribute('id'));
    }

    /**
     * @covers Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder::setActiveNodeVersion
     * @covers Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder::getActiveNodeVersion
     */
    public function testSetGetActiveNodeVersion()
    {
        $nodeVersion = new NodeVersion();
        $this->builder->setActiveNodeVersion($nodeVersion);
        $this->assertEquals($this->builder->getActiveNodeVersion(), $nodeVersion);
    }

    /**
     * @covers Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder::createActionsMenu
     */
    public function testShouldShowDeleteButtonWhenTheNodeHasAParent()
    {
        $nodeTranslation = new NodeTranslation();
        $node = new Node();
        $node->setParent(new Node);
        $nodeTranslation->setNode($node);

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType('public');
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);


        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.delete'));

        $this->assertEquals('page-main-actions js-auto-collapse-buttons', $menu->getChildrenAttribute('class'));
    }

    /**
     * @covers Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder::createActionsMenu
     */
    public function testShouldShowRecopyButtonWhenTheNodeHasTranslations()
    {
        $node = new Node();
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setLang('en');

        $node->addNodeTranslation($nodeTranslation);

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType("public");
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);

        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setLang('nl');

        $node->addNodeTranslation($nodeTranslation);

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType("public");
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);

        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.recopyfromlanguage'));

        $this->assertEquals('page-main-actions js-auto-collapse-buttons', $menu->getChildrenAttribute('class'));
    }

}
