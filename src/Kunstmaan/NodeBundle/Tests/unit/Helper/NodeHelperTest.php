<?php

namespace Kunstmaan\NodeBundle\Tests\Helper;

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Entity\User;
use Kunstmaan\AdminBundle\Helper\CloneHelper;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\NodeBundle\Entity\HasNodeInterface;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Entity\NodeVersion;
use Kunstmaan\NodeBundle\Event\CopyPageTranslationNodeEvent;
use Kunstmaan\NodeBundle\Event\Events;
use Kunstmaan\NodeBundle\Event\NodeEvent;
use Kunstmaan\NodeBundle\Event\RecopyPageTranslationNodeEvent;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeAdminPublisher;
use Kunstmaan\NodeBundle\Helper\NodeAdmin\NodeVersionLockHelper;
use Kunstmaan\NodeBundle\Helper\NodeHelper;
use Kunstmaan\NodeBundle\Repository\NodeRepository;
use Kunstmaan\NodeBundle\Repository\NodeTranslationRepository;
use Kunstmaan\NodeBundle\Repository\NodeVersionRepository;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TestPage extends AbstractPage implements HasNodeInterface
{
    /**
     * @return array
     */
    public function getPossibleChildTypes()
    {
        return [];
    }
}

class NodeHelperTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|EntityManagerInterface $em */
    private $em;

    /** @var \PHPUnit_Framework_MockObject_MockObject|NodeRepository */
    private $repository;

    /** @var NodeHelper */
    private $nodeHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject|NodeAdminPublisher */
    private $nodeAdminPublisher;

    /** @var \PHPUnit_Framework_MockObject_MockObject|EventDispatcher */
    private $eventDispatcher;

    /** @var \PHPUnit_Framework_MockObject_MockObject|TokenStorageInterface */
    private $tokenStorage;

    /** @var \PHPUnit_Framework_MockObject_MockObject|NodeVersionLockHelper */
    private $nodeVersionLockHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject|CloneHelper */
    private $cloneHelper;

    /** @var string */
    private $locale = 'en';

    /** @var User */
    private $user;

    public function setUp()
    {
        $this->createORM();
        $this->nodeHelper = $this->createNodeHelper();
    }

    public function testUpdatePage()
    {
        /**
         * @var TestPage
         * @var NodeTranslation $nodeTranslation
         */
        list($page, $nodeTranslation, $node) = $this->createNodeEntities();
        $nodeVersion = $nodeTranslation->getPublicNodeVersion();

        $this->em
            ->expects($this->exactly(3))
            ->method('persist')
            ->withConsecutive(
                [$this->equalTo($nodeTranslation)],
                [$this->equalTo($nodeVersion)],
                [$this->equalTo($node)]
            );

        $this->eventDispatcher
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(Events::PRE_PERSIST), $this->equalTo(new NodeEvent($node, $nodeTranslation, $nodeVersion, $page))],
                [$this->equalTo(Events::POST_PERSIST), $this->equalTo(new NodeEvent($node, $nodeTranslation, $nodeVersion, $page))]
            );

        $this->nodeHelper->updatePage(
            $node,
            $nodeTranslation,
            $nodeTranslation->getPublicNodeVersion(),
            $page,
            false,
            null
        );
    }

    public function testCreatePage()
    {
        $title = 'Test page';
        $user = new User();

        list($homePage, , $nodeHomePage) = $this->createNodeEntities('Homepage');

        /**
         * @var TestPage
         * @var NodeTranslation $nodeTranslationChildPage
         */
        list($page, $nodeTranslationChildPage, $nodeChildPage) = $this->createNodeEntities($title);

        $expectedTestPageCreateNodeFor = new TestPage();
        $expectedTestPageCreateNodeFor->setTitle($title);
        $expectedTestPageCreateNodeFor->setParent($homePage);

        $nodeRepository = $this->getMockBuilder(NodeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $nodeRepository
            ->expects($this->once())
            ->method('createNodeFor')
            ->with(
                $this->equalTo($expectedTestPageCreateNodeFor),
                $this->equalTo($this->locale),
                $this->equalTo($user)
            )
            ->willReturn($nodeChildPage);

        $nodeTranslationRepository = $this->getMockBuilder(NodeTranslationRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $nodeTranslationRepository
            ->expects($this->once())
            ->method('getMaxChildrenWeight')
            ->willReturn(1);

        $this->em
            ->method('getRepository')
            ->withConsecutive(
                [$this->equalTo('KunstmaanNodeBundle:Node')],
                [$this->equalTo('KunstmaanNodeBundle:NodeTranslation')]
            )
            ->willReturnOnConsecutiveCalls(
                $nodeRepository,
                $nodeTranslationRepository
            );

        $expectedEvent = new NodeEvent(
            $nodeChildPage, $nodeTranslationChildPage, $nodeTranslationChildPage->getPublicNodeVersion(), $expectedTestPageCreateNodeFor
        );
        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::ADD_NODE), $this->equalTo($expectedEvent))
        ;

        $result = $this->nodeHelper->createPage(TestPage::class, $title, $this->locale, $nodeHomePage);

        $this->assertInstanceOf(NodeTranslation::class, $result);
        $this->assertEquals(2, $result->getWeight());
        $this->assertEquals($title, $result->getTitle());
    }

    public function testDeletePage()
    {
        /**
         * @var Node
         * @var NodeTranslation $nodeTranslationHomePage
         */
        list($homePage, $nodeTranslationHomePage, $nodeHomePage) = $this->createNodeEntities('Homepage');
        $nodeVersionHomePage = $nodeTranslationHomePage->getPublicNodeVersion();

        /**
         * @var TestPage
         * @var NodeTranslation $nodeTranslationChildPage
         */
        list($page, $nodeTranslationChildPage, $nodeChildPage) = $this->createNodeEntities('Test page');
        $nodeVersionChildPage = $nodeTranslationChildPage->getPublicNodeVersion();
        $nodeHomePage->addNode($nodeChildPage);

        $this->eventDispatcher
            ->expects($this->exactly(4))
            ->method('dispatch')
            ->withConsecutive(
                [$this->equalTo(Events::PRE_DELETE), $this->equalTo(new NodeEvent($nodeHomePage, $nodeTranslationHomePage, $nodeVersionHomePage, $homePage))],
                [$this->equalTo(Events::PRE_DELETE), $this->equalTo(new NodeEvent($nodeChildPage, $nodeTranslationChildPage, $nodeVersionChildPage, $page))],
                [$this->equalTo(Events::POST_DELETE), $this->equalTo(new NodeEvent($nodeChildPage, $nodeTranslationChildPage, $nodeVersionChildPage, $page))],
                [$this->equalTo(Events::POST_DELETE), $this->equalTo(new NodeEvent($nodeHomePage, $nodeTranslationHomePage, $nodeVersionHomePage, $homePage))]
            );

        $result = $this->nodeHelper->deletePage($nodeHomePage, $this->locale);

        $this->assertTrue($result->getNode()->isDeleted());
        $this->assertTrue($nodeHomePage->isDeleted());
        $this->assertTrue($nodeChildPage->isDeleted());
    }

    public function testPrepareNodeVersionForPublic()
    {
        $user = new User();

        $page = new TestPage();
        $page->setTitle('Test');
        $page->setId(1);

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType(NodeVersion::PUBLIC_VERSION);
        $nodeVersion->setRef($page);

        $nodeVersion = $this->getMockBuilder(NodeVersion::class)->getMock();
        $nodeVersion
            ->method('getType')
            ->willReturn(NodeVersion::PUBLIC_VERSION);
        $nodeVersion
            ->expects($this->once())
            ->method('getRef')
            ->willReturn($page);
        $nodeVersion
            ->method('getUpdated')
            ->willReturn((new \DateTime('-1 hour')));

        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setLang($this->locale);
        $nodeTranslation->addNodeVersion($nodeVersion);

        $this->nodeAdminPublisher
            ->expects($this->once())
            ->method('createPublicVersion')
            ->with(
                $this->equalTo($page),
                $this->equalTo($nodeTranslation),
                $this->equalTo($nodeVersion),
                $this->equalTo($user)
            );

        $this->nodeHelper->prepareNodeVersion($nodeVersion, $nodeTranslation, 10, true);
    }

    public function testPrepareNodeVersionForDraft()
    {
        $page = new TestPage();
        $page->setTitle('Test');
        $page->setId(1);

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType(NodeVersion::PUBLIC_VERSION);
        $nodeVersion->setRef($page);

        $nodeVersion = $this->getMockBuilder(NodeVersion::class)->getMock();
        $nodeVersion
            ->method('getType')
            ->willReturn(NodeVersion::DRAFT_VERSION);
        $nodeVersion
            ->expects($this->once())
            ->method('getRef')
            ->willReturn($page);
        $nodeVersion
            ->method('getUpdated')
            ->willReturn((new \DateTime('-1 hour')));

        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setLang($this->locale);
        $nodeTranslation->addNodeVersion($nodeVersion);

        /** @var \PHPUnit_Framework_MockObject_MockObject|NodeHelper $nodeHelper */
        $nodeHelper = $this->getMockBuilder(NodeHelper::class)
            ->setConstructorArgs([
                $this->em,
                $this->nodeAdminPublisher,
                $this->tokenStorage,
                $this->cloneHelper,
                $this->eventDispatcher,
            ])
            ->setMethods(['createDraftVersion'])
            ->getMock();
        $nodeHelper
            ->expects($this->once())
            ->method('createDraftVersion')
            ->willReturn(true);

        $nodeHelper->prepareNodeVersion($nodeVersion, $nodeTranslation, 10, true);
    }

    public function testCreateDraftVersion()
    {
        /**
         * @var TestPage
         * @var NodeTranslation $nodeTranslation
         */
        list($page, $nodeTranslation, $node) = $this->createNodeEntities();
        $originalNodeVersion = new NodeVersion();

        $this->cloneHelper
            ->expects($this->once())
            ->method('deepCloneAndSave')
            ->willReturn($page);

        $publicNodeVersion = new NodeVersion();
        $publicNodeVersion->setRef($page);
        $publicNodeVersion->setType(NodeVersion::PUBLIC_VERSION);

        $nodeVersionRepository = $this->getMockBuilder(NodeVersionRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $nodeVersionRepository
            ->method('createNodeVersionFor')
            ->willReturn($publicNodeVersion);

        $this->em->method('getRepository')
            ->with('KunstmaanNodeBundle:NodeVersion')
            ->willReturn($nodeVersionRepository);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::CREATE_DRAFT_VERSION), $this->equalTo(new NodeEvent($node, $nodeTranslation, $originalNodeVersion, $page)));

        $result = $this->nodeHelper->createDraftVersion($page, $nodeTranslation, $originalNodeVersion);

        $this->assertInstanceOf(NodeVersion::class, $result);
        $this->assertEquals(NodeVersion::DRAFT_VERSION, $result->getType());
        $this->assertEquals($publicNodeVersion, $result->getOrigin());
    }

    public function testGetPageWithNodeInterface()
    {
        $refId = 10;

        $page = new TestPage();
        $page->setTitle('Test');
        $page->setId($refId);

        $nodeVersion = new NodeVersion();
        $nodeVersion->setType(NodeVersion::PUBLIC_VERSION);
        $nodeVersion->setRef($page);

        $nodeTranslation = new NodeTranslation();
        $nodeTranslation->setLang($this->locale);
        $nodeTranslation->addNodeVersion($nodeVersion);

        $node = new Node();
        $node->addNodeTranslation($nodeTranslation);

        $repository = $this->getMockBuilder(ObjectRepository::class)->getMock();
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($refId)
            ->willReturn($page);

        $this->em
            ->expects($this->once())
            ->method('getRepository')
            ->with($this->equalTo(TestPage::class))
            ->willReturn($repository);

        $this->nodeHelper->getPageWithNodeInterface($node, $this->locale);
    }

    public function testCopyPageFromOtherLanguage()
    {
        $targetLocale = 'nl';
        $targetPage = new TestPage();

        /**
         * @var TestPage
         * @var NodeTranslation $sourceNodeTranslation
         */
        list($sourcePage, $sourceNodeTranslation, $node) = $this->createNodeEntities();
        $sourceNodeNodeVersion = $sourceNodeTranslation->getPublicNodeVersion();

        $this->cloneHelper
            ->expects($this->once())
            ->method('deepCloneAndSave')
            ->with($sourcePage)
            ->willReturn($targetPage);

        $expectedNodeVersion = new NodeVersion();
        $expectedNodeVersion->setType(NodeVersion::PUBLIC_VERSION);
        $expectedNodeVersion->setRef($targetPage);

        $expectedNodeTranslation = new NodeTranslation();
        $expectedNodeTranslation->setNode($node);
        $expectedNodeTranslation->setLang($targetLocale);
        $expectedNodeTranslation->setPublicNodeVersion($expectedNodeVersion);

        $repository = $this->getMockBuilder(NodeTranslationRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->once())
            ->method('createNodeTranslationFor')
            ->with($this->equalTo($targetPage), $this->equalTo($targetLocale), $this->equalTo($node), $this->equalTo($this->user))
            ->willReturn($expectedNodeTranslation);

        $this->em
            ->expects($this->once())
            ->method('getRepository')
            ->with(NodeTranslation::class)
            ->willReturn($repository);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::COPY_PAGE_TRANSLATION), $this->equalTo(new CopyPageTranslationNodeEvent(
                $node,
                $expectedNodeTranslation,
                $expectedNodeVersion,
                $targetPage,
                $sourceNodeTranslation,
                $sourceNodeNodeVersion,
                $sourcePage,
                $this->locale)));

        $result = $this->nodeHelper->copyPageFromOtherLanguage($node, $this->locale, $targetLocale);

        $this->assertInstanceOf(NodeTranslation::class, $result);
        $this->assertEquals($expectedNodeTranslation, $result);
    }

    public function testDuplicatePage()
    {
        $targetPage = new TestPage();

        list(, $nodeTranslationHomePage, $nodeHomePage) = $this->createNodeEntities('Homepage');

        /**
         * @var TestPage
         * @var NodeTranslation $sourceNodeTranslation
         * @var Node            $node
         */
        list($sourcePage, $sourceNodeTranslation, $node) = $this->createNodeEntities();
        $node->setParent($nodeHomePage);

        $this->cloneHelper
            ->expects($this->once())
            ->method('deepCloneAndSave')
            ->with($sourcePage)
            ->willReturn($targetPage);

        $expectedNodeTranslation = new NodeTranslation();
        $expectedNodeTranslation->setLang($this->locale);

        $expectedNode = new Node();
        $expectedNode->addNodeTranslation($expectedNodeTranslation);

        $repository = $this->getMockBuilder(NodeRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->once())
            ->method('createNodeFor')
            ->willReturn($expectedNode);

        $this->em
            ->expects($this->once())
            ->method('getRepository')
            ->with(Node::class)
            ->willReturn($repository);

        $result = $this->nodeHelper->duplicatePage($node, $this->locale);

        $this->assertInstanceOf(NodeTranslation::class, $result);
    }

    public function testCreatePageDraftFromOtherLanguage()
    {
        $targetLocale = 'nl';
        $targetPage = new TestPage();

        /**
         * @var TestPage
         * @var NodeTranslation $sourceNodeTranslation
         */
        list($sourcePage, $sourceNodeTranslation, $node) = $this->createNodeEntities();
        $sourceNodeNodeVersion = $sourceNodeTranslation->getPublicNodeVersion();

        $this->cloneHelper
            ->expects($this->once())
            ->method('deepCloneAndSave')
            ->with($sourcePage)
            ->willReturn($targetPage);

        $expectedNodeVersion = new NodeVersion();
        $expectedNodeVersion->setType(NodeVersion::PUBLIC_VERSION);
        $expectedNodeVersion->setRef($targetPage);

        $expectedNodeTranslation = new NodeTranslation();
        $expectedNodeTranslation->setNode($node);
        $expectedNodeTranslation->setLang($targetLocale);
        $expectedNodeTranslation->setPublicNodeVersion($expectedNodeVersion);

        $repository = $this->getMockBuilder(NodeTranslationRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->once())
            ->method('find')
            ->with($this->equalTo(1))
            ->willReturn($sourceNodeTranslation);
        $repository
            ->expects($this->once())
            ->method('addDraftNodeVersionFor')
            ->with($this->equalTo($targetPage), $this->equalTo($targetLocale), $this->equalTo($node), $this->equalTo($this->user))
            ->willReturn($expectedNodeTranslation);

        $this->em
            ->expects($this->exactly(2))
            ->method('getRepository')
            ->with(NodeTranslation::class)
            ->willReturn($repository);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::RECOPY_PAGE_TRANSLATION), $this->equalTo(new RecopyPageTranslationNodeEvent(
                $node,
                $expectedNodeTranslation,
                $expectedNodeVersion,
                $targetPage,
                $sourceNodeTranslation,
                $sourceNodeNodeVersion,
                $sourcePage,
                $this->locale)));

        $result = $this->nodeHelper->createPageDraftFromOtherLanguage($node, 1, $targetLocale);

        $this->assertInstanceOf(NodeTranslation::class, $result);
        $this->assertEquals($expectedNodeTranslation, $result);
    }

    public function testCreateEmptyPage()
    {
        $targetPage = new TestPage();
        $targetPage->setTitle('No title');
        $node = new Node();
        $node->setRef($targetPage);

        $expectedNodeVersion = new NodeVersion();
        $expectedNodeVersion->setType(NodeVersion::PUBLIC_VERSION);
        $expectedNodeVersion->setRef($targetPage);

        $expectedNodeTranslation = new NodeTranslation();
        $expectedNodeTranslation->setNode($node);
        $expectedNodeTranslation->setLang($this->locale);
        $expectedNodeTranslation->setPublicNodeVersion($expectedNodeVersion);

        $repository = $this->getMockBuilder(NodeTranslationRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository
            ->expects($this->once())
            ->method('createNodeTranslationFor')
            ->with($this->equalTo($targetPage), $this->equalTo($this->locale), $this->equalTo($node), $this->equalTo($this->user))
            ->willReturn($expectedNodeTranslation);

        $this->em
            ->expects($this->once())
            ->method('getRepository')
            ->with(NodeTranslation::class)
            ->willReturn($repository);

        $this->eventDispatcher
            ->expects($this->once())
            ->method('dispatch')
            ->with($this->equalTo(Events::ADD_EMPTY_PAGE_TRANSLATION), $this->equalTo(new NodeEvent(
                $node,
                $expectedNodeTranslation,
                $expectedNodeVersion,
                $targetPage)));

        $result = $this->nodeHelper->createEmptyPage($node, $this->locale);

        $this->assertInstanceOf(NodeTranslation::class, $result);
        $this->assertEquals($expectedNodeTranslation, $result);
    }

    private function createORM()
    {
        $this->repository = $this->getMockBuilder(ObjectRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return NodeHelper
     */
    private function createNodeHelper()
    {
        $this->user = new User();

        $token = $this->getMockBuilder(TokenInterface::class)->getMock();
        $token->method('getUser')->willReturn($this->user);

        $this->tokenStorage = $this->getMockBuilder(TokenStorage::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->tokenStorage->method('getToken')->willReturn($token);
        $this->nodeAdminPublisher = $this->getMockBuilder(NodeAdminPublisher::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->cloneHelper = $this->getMockBuilder(CloneHelper::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcher::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new NodeHelper(
            $this->em,
            $this->nodeAdminPublisher,
            $this->tokenStorage,
            $this->cloneHelper,
            $this->eventDispatcher
        );
    }

    /**
     * @param string $title
     *
     * @return array
     */
    private function createNodeEntities($title = 'Test page')
    {
        $testPage = new TestPage();
        $testPage->setTitle($title);

        $nodeVersionNewPage = $this->getMockBuilder(NodeVersion::class)->getMock();
        $nodeVersionNewPage
            ->method('getRef')
            ->with($this->em)
            ->willReturn($testPage);
        $nodeVersionNewPage
            ->method('getType')
            ->willReturn(NodeVersion::PUBLIC_VERSION);

        $nodeTranslationNewPage = new NodeTranslation();
        $nodeTranslationNewPage->setTitle($title);
        $nodeTranslationNewPage->setLang($this->locale);
        $nodeTranslationNewPage->addNodeVersion($nodeVersionNewPage);
        $nodeTranslationNewPage->setOnline(true);

        $nodeNewPage = new Node();
        $nodeNewPage->setDeleted(false);
        $nodeNewPage->addNodeTranslation($nodeTranslationNewPage);

        return [$testPage, $nodeTranslationNewPage, $nodeNewPage];
    }
}
