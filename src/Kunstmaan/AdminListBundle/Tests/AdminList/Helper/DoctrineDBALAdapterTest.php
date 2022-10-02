<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Helper;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Schema\Schema;
use Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

class DoctrineDBALAdapterTest extends TestCase
{
    use ExpectDeprecationTrait;

    /** @var Connection */
    private $connection;
    /** @var QueryBuilder */
    private $qb;

    protected function setUp(): void
    {
        $this->connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);

        $this->createSchema();
        $this->insertData();

        $this->qb = new QueryBuilder($this->connection);
        $this->qb->select('p.*')->from('posts', 'p');
    }

    private function createSchema(): void
    {
        $schema = new Schema();
        $posts = $schema->createTable('posts');
        $posts->addColumn('id', 'integer', ['unsigned' => true, 'autoincrement' => true]);
        $posts->addColumn('username', 'string', ['length' => 32]);
        $posts->addColumn('post_content', 'text');
        $posts->setPrimaryKey(['id']);

        $queries = $schema->toSql($this->connection->getDatabasePlatform()); // get queries to create this schema.

        foreach ($queries as $sql) {
            $this->connection->executeQuery($sql);
        }
    }

    private function insertData(): void
    {
        $this->connection->transactional(
            static function (Connection $connection): void {
                for ($i = 1; $i <= 50; ++$i) {
                    $connection->insert('posts', ['username' => 'Jon Doe', 'post_content' => 'Post #' . $i]);
                }
            }
        );
    }

    /**
     * Mark test as legacy to avoid "\Pagerfanta\Exception\Exception" interface deprecation
     *
     * @group legacy
     */
    public function testConstructorWithIncorrectCountField()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');

        $this->expectExceptionMessage('The $countField must contain a table alias in the string.');
        $this->expectException(\LogicException::class);

        new DoctrineDBALAdapter($this->qb, 'id');
    }

    /**
     * @group legacy
     */
    public function testGetQueryBuilder()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');
        $adapter = new DoctrineDBALAdapter($this->qb, 'p.id');

        $this->assertInstanceOf(QueryBuilder::class, $adapter->getQueryBuilder());
    }

    /**
     * @group legacy
     */
    public function testConstructorThrowsAnotherException()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');

        $this->expectExceptionMessage('Only SELECT queries can be paginated.');
        $this->expectException(\LogicException::class);

        $qb = new QueryBuilder($this->connection);
        $qb->delete('posts', 'p');

        new DoctrineDBALAdapter($qb, 'p.id');
    }

    /**
     * @group legacy
     */
    public function testGetSlice()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');

        $adapter = new DoctrineDBALAdapter($this->qb, 'p.id');

        $offset = 30;
        $length = 10;

        $this->qb->setFirstResult($offset)->setMaxResults($length);

        if (method_exists($this->qb, 'executeQuery')) {
            $stmt = $this->qb->executeQuery();
        } else {
            $stmt = $this->qb->execute();
        }

        $this->assertSame($stmt->fetchAllAssociative(), $adapter->getSlice($offset, $length));
    }

    /**
     * @group legacy
     */
    public function testNbResults()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');

        $adapter = new DoctrineDBALAdapter($this->qb, 'p.id');

        $this->assertSame(50, $adapter->getNbResults());
    }

    /**
     * @group legacy
     */
    public function testNbResultsWithZeroResults()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');

        $qb = new QueryBuilder($this->connection);
        $qb->select('p.*')->from('posts', 'p')->where('username = :username')->setParameter('username', 'Non-existing');

        $adapter = new DoctrineDBALAdapter($qb, 'p.id');

        $this->assertSame(0, $adapter->getNbResults());
    }
}
