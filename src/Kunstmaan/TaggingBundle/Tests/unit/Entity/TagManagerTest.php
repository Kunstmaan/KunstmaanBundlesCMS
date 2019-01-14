<?php

namespace Kunstmaan\TaggingBundle\Tests\Entity;

use Closure;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\MySqlPlatform;
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
use PHPUnit_Framework_TestCase;

class Query extends AbstractQuery
{
    public function getSQL()
    {
    }

    protected function _doExecute()
    {
    }

    public function getResult($hydrationMode = self::HYDRATE_OBJECT)
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

    public function getTaggableId()
    {
        return 777;
    }

    public function getTaggableType()
    {
        return self::class;
    }
}

class Lazy implements LazyLoadingTaggableInterface
{
    use TaggableTrait;

    private $loader;

    public function setTagLoader(Closure $loader)
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

/**
 * Class TagManagerTest
 */
class TagManagerTest extends PHPUnit_Framework_TestCase
{
    /** @var TagManager $object */
    private $object;

    private $em;

    public function setUp()
    {
        $tag = new Tag();

        $comparison = $this->getMockBuilder(Expr\Comparison::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expr = $this->getMockBuilder(Expr::class)
            ->disableOriginalConstructor()
            ->getMock();

        $expr->expects($this->any())
            ->method('eq')
            ->will($this->returnValue($comparison));

        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder = $this->getMockBuilder(QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $builder->expects($this->any())
            ->method('select')
            ->will($this->returnValue($builder));

        $builder->expects($this->any())
            ->method('from')
            ->will($this->returnValue($builder));

        $builder->expects($this->any())
            ->method('where')
            ->will($this->returnValue($builder));

        $builder->expects($this->any())
            ->method('expr')
            ->will($this->returnValue($expr));

        $builder->expects($this->any())
            ->method('getQuery')
            ->will($this->returnValue($query));

        $builder->expects($this->any())
            ->method('innerJoin')
            ->will($this->returnValue($builder));

        $builder->expects($this->any())
            ->method('setParameter')
            ->will($this->returnValue($builder));

        $query->expects($this->any())
            ->method('getOneOrNullResult')
            ->will($this->returnValue(new Tag()));

        $query->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue([]));

        $em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo = $this->getMockBuilder(EntityRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repo->expects($this->any())
            ->method('find')
            ->will($this->returnValue($tag));

        $repo->expects($this->any())
        ->method('findAll')
        ->will($this->returnValue(new ArrayCollection()));

        $em->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($repo));

        $em->expects($this->any())
            ->method('createQueryBuilder')
            ->will($this->returnValue($builder));

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

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->any())
            ->method('getDatabasePlatform')
            ->will($this->returnValue(new MySqlPlatform()));

        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue(new ArrayCollection()));

        $this->em->expects($this->any())
            ->method('createNativeQuery')
            ->will($this->returnValue($query));

        $this->em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $this->em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $this->object = new TagManager($this->em);

        $item = new Random();
        $results = $this->object->findRelatedItems($item, Random::class, 'en');
        $this->assertInstanceOf(ArrayCollection::class, $results);
    }

    public function testFindRelatedItemsReturnsNull()
    {
        $item = new Random();
        $results = $this->object->findRelatedItems($item, DateTime::class, 'en');
        $this->assertNull($results);
    }

    public function testFindRelatedItemsWithAbstractPage()
    {
        $meta = new ClassMetadata(Random::class);

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects($this->any())
            ->method('getDatabasePlatform')
            ->will($this->returnValue(new MySqlPlatform()));

        $this->em->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        $query = $this->getMockBuilder(Query::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue(new ArrayCollection()));

        $this->em->expects($this->any())
            ->method('createNativeQuery')
            ->will($this->returnValue($query));

        $this->em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $this->em->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue($meta));

        $item = new FakePage();
        $results = $this->object->findRelatedItems($item, Random::class, 'en');
        $this->assertInstanceOf(ArrayCollection::class, $results);
    }

    public function testTagging()
    {
        $config = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->any())
            ->method('getCustomHydrationMode')
            ->will($this->returnValue(null));

        $config->expects($this->any())
            ->method('addCustomHydrationMode')
            ->will($this->returnValue(null));

        $this->em->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($config));

        $resource = new FakePage();
        $this->assertNull($this->object->loadTagging($resource));
    }

    public function testLoadTagging()
    {
        $config = $this->getMockBuilder(Configuration::class)
            ->disableOriginalConstructor()
            ->getMock();

        $config->expects($this->any())
            ->method('getCustomHydrationMode')
            ->will($this->returnValue(null));

        $config->expects($this->any())
            ->method('addCustomHydrationMode')
            ->will($this->returnValue(null));

        $this->em->expects($this->any())
            ->method('getConfiguration')
            ->will($this->returnValue($config));

        $resource = new Lazy();
        $this->assertNull($this->object->loadTagging($resource));
        $loader = $resource->getLoader();
        $loader($resource);
    }
}
