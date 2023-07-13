<?php

namespace Kunstmaan\TaggingBundle\Tests\Entity;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Result;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\NodeBundle\Entity\AbstractPage;
use Kunstmaan\TaggingBundle\Entity\LazyLoadingTaggableInterface;
use Kunstmaan\TaggingBundle\Entity\Tag;
use Kunstmaan\TaggingBundle\Entity\Taggable;
use Kunstmaan\TaggingBundle\Entity\TaggableTrait;
use Kunstmaan\TaggingBundle\Entity\TagManager;
use PHPUnit\Framework\TestCase;

class Query extends AbstractQuery
{
    /**
     * @return string
     */
    public function getSQL()
    {
    }

    /**
     * @return Result|int
     */
    protected function _doExecute()
    {
    }

    public function getResult($hydrationMode = self::HYDRATE_OBJECT): mixed
    {
        return new ArrayCollection();
    }
}

class FakePage extends AbstractPage implements Taggable
{
    use TaggableTrait;

    public function getPossibleChildTypes()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getTaggableId()
    {
        return 777;
    }

    /**
     * @return string
     */
    public function getTaggableType()
    {
        return self::class;
    }
}

class Lazy implements LazyLoadingTaggableInterface
{
    use TaggableTrait;

    private ?\Closure $loader = null;

    public function setTagLoader(\Closure $loader)
    {
        $this->loader = $loader;
    }

    public function getLoader()
    {
        return $this->loader;
    }

    public function getId()
    {
        return 5;
    }
}

class TagManagerTest extends TestCase
{
    private TagManager $object;

    private $em;

    public function setUp(): void
    {
        $tag = new Tag();

        $comparison = $this->getMockBuilder(Comparison::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder(Expr::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expr
            ->method('eq')
            ->willReturn($comparison);

        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder
            ->method('select')
            ->willReturn($builder);

        $builder
            ->method('from')
            ->willReturn($builder);

        $builder
            ->method('where')
            ->willReturn($builder);

        $builder
            ->method('expr')
            ->willReturn($expr);

        $builder
            ->method('getQuery')
            ->willReturn($query);

        $builder
            ->method('innerJoin')
            ->willReturn($builder);

        $builder
            ->method('setParameter')
            ->willReturn($builder);

        $query
            ->method('getOneOrNullResult')
            ->willReturn(new Tag());

        $query
            ->method('getResult')
            ->willReturn([]);

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo
            ->method('find')
            ->willReturn($tag);

        $repo
        ->method('findAll')
        ->willReturn(new ArrayCollection());

        $em
            ->method('getRepository')
            ->willReturn($repo);

        $em
            ->method('createQueryBuilder')
            ->willReturn($builder);

        $this->object = new TagManager($em);

        $this->em = $em;
    }

    public function testFindById()
    {
        $result = $this->object->findById(666);

        $this->assertInstanceOf(Tag::class, $result);
    }

    public function testFindByIdReturnsNull()
    {
        $em = $this->getMockBuilder(EntityManager::class)
        ->disableOriginalConstructor()
        ->getMock();

        $object = new TagManager($em);
        $this->assertNull($object->findById(null));
    }

    public function testFindAll()
    {
        $this->assertInstanceOf(ArrayCollection::class, $this->object->findAll());
    }

    public function testFindRelatedItems()
    {
        $meta = new ClassMetadata(Random::class);
        $meta->table = ['name' => 'test_table'];

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection
            ->method('getDatabasePlatform')
            ->willReturn(new MySQL57Platform());

        $this->em
            ->method('getConnection')
            ->willReturn($connection);

        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query
            ->method('getResult')
            ->willReturn(new ArrayCollection());

        $this->em
            ->method('createNativeQuery')
            ->willReturn($query);

        $this->em
            ->method('getClassMetadata')
            ->willReturn($meta);

        $this->em
            ->method('getClassMetadata')
            ->willReturn($meta);

        $this->object = new TagManager($this->em);

        $item = new Random();
        $results = $this->object->findRelatedItems($item, Random::class, 'en');
        $this->assertInstanceOf(ArrayCollection::class, $results);
    }

    public function testFindRelatedItemsReturnsNull()
    {
        $item = new Random();
        $results = $this->object->findRelatedItems($item, \DateTime::class, 'en');
        $this->assertNull($results);
    }

    public function testFindRelatedItemsWithAbstractPage()
    {
        $meta = new ClassMetadata(Random::class);
        $meta->table = ['name' => 'test_table'];

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection
            ->method('getDatabasePlatform')
            ->willReturn(new MySQL57Platform());

        $this->em
            ->method('getConnection')
            ->willReturn($connection);

        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query
            ->method('getResult')
            ->willReturn(new ArrayCollection());

        $this->em
            ->method('createNativeQuery')
            ->willReturn($query);

        $this->em
            ->method('getClassMetadata')
            ->willReturn($meta);

        $this->em
            ->method('getClassMetadata')
            ->willReturn($meta);

        $item = new FakePage();
        $results = $this->object->findRelatedItems($item, Random::class, 'en');
        $this->assertInstanceOf(ArrayCollection::class, $results);
    }

    public function testTagging()
    {
        $config = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config
            ->method('getCustomHydrationMode')
            ->willReturn(null);

        $config
            ->method('addCustomHydrationMode')
            ->willReturn(null);

        $this->em
            ->method('getConfiguration')
            ->willReturn($config);

        $resource = new FakePage();
        $this->assertNull($this->object->loadTagging($resource));
    }

    public function testLoadTagging()
    {
        $config = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config
            ->method('getCustomHydrationMode')
            ->willReturn(null);

        $config
            ->method('addCustomHydrationMode')
            ->willReturn(null);

        $this->em
            ->method('getConfiguration')
            ->willReturn($config);

        $resource = new Lazy();
        $this->assertNull($this->object->loadTagging($resource));
        $loader = $resource->getLoader();
        $loader($resource);
    }
}
