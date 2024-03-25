<?php

namespace Kunstmaan\ArticleBundle\Tests\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticlePageAdminListConfigurator;
use Kunstmaan\ArticleBundle\Entity\AbstractArticlePage;
use Kunstmaan\NodeBundle\Entity\Node;
use Kunstmaan\NodeBundle\Entity\NodeTranslation;
use Kunstmaan\NodeBundle\Helper\NodeMenu;
use Kunstmaan\NodeBundle\Helper\NodeMenuItem;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

class Configurator extends AbstractArticlePageAdminListConfigurator
{
    /** @var EntityRepository */
    private $repo;

    public function __construct(EntityManagerInterface $em, AclHelper $aclHelper, $locale, $permission, $repo)
    {
        parent::__construct($em, $aclHelper, $locale, $permission);
        $this->repo = $repo;
    }

    public function getOverviewPageRepository(): \Doctrine\ORM\EntityRepository
    {
        return $this->repo;
    }
}

class AbstractArticlePageAdminListConfiguratorTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @var AbstractArticlePageAdminListConfigurator
     */
    protected $object;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    protected function setUp(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->any())
            ->method($this->anything())
            ->willReturn($em);

        $acl = $this->createMock(AclHelper::class);

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->any())
            ->method($this->anything())
            ->willReturn([['fake' => 'array']]);

        $this->em = $em;

        /* @var EntityManagerInterface $em */
        /* @var AclHelper $acl */
        $this->object = new Configurator($em, $acl, 'nl', 'admin', $repo);
    }

    /**
     * @group legacy
     */
    public function testDeprecatedGetters()
    {
        $this->expectDeprecation('Since kunstmaan/article-bundle 6.4: Method "Kunstmaan\ArticleBundle\AdminList\AbstractArticlePageAdminListConfigurator::getBundleName" deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.');
        $this->expectDeprecation('Since kunstmaan/article-bundle 6.4: Method "Kunstmaan\ArticleBundle\AdminList\AbstractArticlePageAdminListConfigurator::getEntityName" deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.');

        $this->assertEquals('KunstmaanArticleBundle', $this->object->getBundleName());
        $this->assertEquals('AbstractArticlePage', $this->object->getEntityName());
    }

    public function testGetters()
    {
        $this->assertEquals(AbstractArticlePage::class, $this->object->getEntityClass());
        $this->assertEquals('@KunstmaanArticle/AbstractArticlePageAdminList/list.html.twig', $this->object->getListTemplate());
        $this->assertEquals(AbstractArticlePage::class, $this->object->getRepositoryName());
    }

    public function testBuildFields()
    {
        $this->object->buildFields();
        $this->object->buildFilters();
        $fields = $this->object->getFields();
        $this->assertCount(4, $fields);
    }

    public function testGetUrls()
    {
        $node = new Node();
        $node->setId(1314);
        $nodeTranslation = new NodeTranslation();
        /** @var NodeMenu $menu */
        $menu = $this->createMock(NodeMenu::class);
        $item = new NodeMenuItem($node, $nodeTranslation, false, $menu);

        $url = $this->object->getEditUrlFor($item);

        $this->assertCount(2, $url);
        $this->assertArrayHasKey('path', $url);
        $this->assertArrayHasKey('params', $url);
        $this->assertEquals('KunstmaanNodeBundle_nodes_edit', $url['path']);
        $this->assertCount(1, $url['params']);
        $this->assertEquals(1314, $url['params']['id']);

        $url = $this->object->getDeleteUrlFor($item);

        $this->assertCount(2, $url);
        $this->assertArrayHasKey('path', $url);
        $this->assertArrayHasKey('params', $url);
        $this->assertEquals('KunstmaanNodeBundle_nodes_delete', $url['path']);
        $this->assertCount(1, $url['params']);
        $this->assertEquals(1314, $url['params']['id']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetQueryBuilder()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $qb = $this->createMock(QueryBuilder::class);
        $em->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($qb);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($em);

        $this->em = $em;

        $mirror = new \ReflectionClass(Configurator::class);
        $method = $mirror->getMethod('getQueryBuilder');
        $method->setAccessible(true);

        $mirror = new \ReflectionClass(Configurator::class);
        $prop = $mirror->getProperty('em');
        $prop->setAccessible(true);
        $prop->setValue($this->object, $em);

        /* @var QueryBuilder $qb */
        $this->object->adaptQueryBuilder($qb);
        $qb = $method->invoke($this->object);
        $this->assertInstanceOf(QueryBuilder::class, $qb);
    }

    /**
     * @throws \ReflectionException
     */
    public function testEntityClassName()
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $repo = $this->createMock(EntityRepository::class);

        $em->expects($this->any())
            ->method('getRepository')
            ->willReturn($repo);

        $repo->expects($this->any())
            ->method('getClassName')
            ->willReturn(Configurator::class);

        $this->em = $em;

        $mirror = new \ReflectionClass(Configurator::class);
        $method = $mirror->getMethod('getQueryBuilder');
        $method->setAccessible(true);

        $mirror = new \ReflectionClass(Configurator::class);
        $prop = $mirror->getProperty('em');
        $prop->setAccessible(true);
        $prop->setValue($this->object, $em);
        $this->assertEquals(Configurator::class, $this->object->getEntityClassName());
    }

    /**
     * @throws \ReflectionException
     */
    public function testGetOverViewPages()
    {
        $this->assertCount(1, $this->object->getOverviewPages());
        $this->assertCount(1, $this->object->getOverviewPage());

        $mirror = new \ReflectionClass(Configurator::class);
        $prop = $mirror->getProperty('repo');
        $prop->setAccessible(true);

        $repo = $this->createMock(EntityRepository::class);
        $repo->expects($this->any())
            ->method($this->anything())
            ->willReturn([]);

        $prop->setValue($this->object, $repo);

        $this->assertNull($this->object->getOverviewPage());
    }
}
