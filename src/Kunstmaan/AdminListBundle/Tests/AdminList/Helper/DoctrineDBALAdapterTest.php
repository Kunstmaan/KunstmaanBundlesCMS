<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList\Helper;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

class DoctrineDBALAdapterTest extends TestCase
{
    use ExpectDeprecationTrait;

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
        $qb = $this->createMock(QueryBuilder::class);
        new DoctrineDBALAdapter($qb, 'somefield');
    }

    /**
     * @group legacy
     */
    public function testGetQueryBuilder()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('getType')->willReturn(QueryBuilder::SELECT);
        $adapter = new DoctrineDBALAdapter($qb, 'table.somefield');

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
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('getType')->willReturn(QueryBuilder::DELETE);

        new DoctrineDBALAdapter($qb, 'table.somefield');
    }

    /**
     * @group legacy
     */
    public function testGetSlice()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');

        $length = 3;

        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())->method('fetchAll')->willReturn([1, 2, 3]);
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('getType')->willReturn(QueryBuilder::SELECT);
        $qb->expects($this->once())->method('setFirstResult')->willReturn($qb);
        $qb->expects($this->once())->method('setMaxResults')->with($length)->willReturn($qb);
        $qb->expects($this->once())->method('execute')->willReturn($statement);

        $adapter = new DoctrineDBALAdapter($qb, 'table.somefield');
        $result = $adapter->getSlice(0, $length);
        $this->assertCount($length, $result);
    }

    /**
     * @group legacy
     */
    public function testNbResults()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');

        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())->method('fetchColumn')->with(0)->willReturn([1, 2, 3]);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('getType')->willReturn(QueryBuilder::SELECT);
        $qb->expects($this->once())->method('select')->willReturn($qb);
        $qb->expects($this->once())->method('execute')->willReturn($statement);

        $adapter = new DoctrineDBALAdapter($qb, 'table.somefield');
        $result = $adapter->getNbResults();
        $this->assertCount(3, $result);
    }

    /**
     * @group legacy
     */
    public function testNbResultsWithZeroResults()
    {
        $this->expectDeprecation('Since kunstmaan/adminlist-bundle 6.2: Class "Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter" is deprecated, Use the dbal query adapter of "pagerfanta/doctrine-dbal-adapter" instead.');

        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())->method('fetchColumn')->with(0)->willReturn(null);
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('getType')->willReturn(QueryBuilder::SELECT);
        $qb->expects($this->once())->method('select')->willReturn($qb);
        $qb->expects($this->once())->method('execute')->willReturn($statement);

        $adapter = new DoctrineDBALAdapter($qb, 'table.somefield');
        $result = $adapter->getNbResults();
        $this->assertSame(0, $result);
    }
}
