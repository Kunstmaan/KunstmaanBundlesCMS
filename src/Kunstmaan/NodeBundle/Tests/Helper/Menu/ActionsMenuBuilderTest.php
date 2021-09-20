<?php

namespace Kunstmaan\NodeBundle\Tests\Helper\Menu;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Knp\Menu\Integration\Symfony\RoutingExtension;
use Knp\Menu\MenuFactory;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Helper\Menu\ActionsMenuBuilder;
use Kunstmaan\NodeBundle\Helper\PagesConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ActionsMenuBuilderTest extends TestCase
{
    /**
     * @var ActionsMenuBuilder
     */
    protected $builder;

    protected function setUp(): void
    {
        /* @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $this->createMock('Symfony\Component\Routing\Generator\UrlGeneratorInterface');
        $routingExtension = new RoutingExtension($urlGenerator);
        $factory = new MenuFactory();
        $factory->addExtension($routingExtension);
        $em = $this->getMockedEntityManager();
        /* @var EventDispatcherInterface $dispatcher */
        $dispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        /* @var RouterInterface $router */
        $router = $this->createMock('Symfony\Component\Routing\RouterInterface');
        $authorizationChecker = $this->createMock('Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface');
        $authorizationChecker->expects($this->any())
            ->method('isGranted')
            ->willReturn(true);

        $this->builder = new ActionsMenuBuilder($factory, $em, $router, $dispatcher, $authorizationChecker, new PagesConfiguration([]));
    }

    /**
     * @throws \Exception
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getMockedEntityManager()
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->willReturn(null);
        $repository->method('findBy')->willReturn(null);
        $repository->method('findOneBy')->willReturn(null);

        $emMock = $this->createMock(EntityManager::class);
        $emMock->method('getRepository')->willReturn($repository);
        $emMock->method('getClassMetaData')->willReturn((object) ['name' => 'aClass']);
        $emMock->method('persist')->willReturn(null);
        $emMock->method('flush')->willReturn(null);

        return $emMock;
    }

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

        if (null !== $nodeTranslation->getNode()->getParent() || $nodeTranslation->getNode()->getChildren()->isEmpty()) {
            $this->assertNotNull($menu->getChild('action.delete'));
        } else {
            $this->assertNull($menu->getChild('action.delete'));
        }

        $this->assertEquals('page-main-actions js-auto-collapse-buttons', $menu->getChildrenAttribute('class'));
    }

    public function testCreateActionsMenuPublic()
    {
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setNode(new Node());

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType('public');
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);

        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.save'));
        $this->assertNotNull($menu->getChild('action.saveasdraft'));
        $this->assertNull($menu->getChild('action.recopyfromlanguage'));
        $this->assertNotNull($menu->getChild('action.preview'));
        $this->assertNotNull($menu->getChild('action.publish'));
        $this->assertNull($menu->getChild('action.unpublish'));
        if (null !== $nodeTranslation->getNode()->getParent() || $nodeTranslation->getNode()->getChildren()->isEmpty()) {
            $this->assertNotNull($menu->getChild('action.delete'));
        } else {
            $this->assertNull($menu->getChild('action.delete'));
        }

        $nodeTranslation->setOnline(true);
        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.save'));
        $this->assertNotNull($menu->getChild('action.saveasdraft'));
        $this->assertNull($menu->getChild('action.recopyfromlanguage'));
        $this->assertNotNull($menu->getChild('action.preview'));
        $this->assertNull($menu->getChild('action.publish'));
        $this->assertNotNull($menu->getChild('action.unpublish'));
        if (null !== $nodeTranslation->getNode()->getParent() || $nodeTranslation->getNode()->getChildren()->isEmpty()) {
            $this->assertNotNull($menu->getChild('action.delete'));
        } else {
            $this->assertNull($menu->getChild('action.delete'));
        }

        $this->assertEquals('page-main-actions js-auto-collapse-buttons', $menu->getChildrenAttribute('class'));
    }

    public function testCreateActionsMenuNonEditable()
    {
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setNode(new Node());

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType('public');
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

    public function testSetGetActiveNodeVersion()
    {
        $nodeVersion = new NodeVersion();
        $this->builder->setActiveNodeVersion($nodeVersion);
        $this->assertEquals($this->builder->getActiveNodeVersion(), $nodeVersion);
    }

    public function testShouldShowDeleteButtonWhenTheNodeHasAParent()
    {
        $nodeTranslation = new NodeTranslation();
        $node = new Node();
        $node->setParent(new Node());
        $nodeTranslation->setNode($node);

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType('public');
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);

        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.delete'));

        $this->assertEquals('page-main-actions js-auto-collapse-buttons', $menu->getChildrenAttribute('class'));
    }

    public function testShouldShowRecopyButtonWhenTheNodeHasTranslations()
    {
        $node = new Node();
        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setLang('en');

        $node->addNodeTranslation($nodeTranslation);

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType('public');
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);

        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setLang('nl');

        $node->addNodeTranslation($nodeTranslation);

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType('public');
        $nodeVersion->setNodeTranslation($nodeTranslation);

        $this->builder->setActiveNodeVersion($nodeVersion);

        $menu = $this->builder->createActionsMenu();
        $this->assertNotNull($menu->getChild('action.recopyfromlanguage'));

        $this->assertEquals('page-main-actions js-auto-collapse-buttons', $menu->getChildrenAttribute('class'));
    }
}
