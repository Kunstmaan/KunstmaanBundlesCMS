<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter;
use PHPUnit\Framework\TestCase;

/**
 * Class AdminListTest
 */
class DoctrineDBALAdapterTest extends TestCase
{
    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage The $countField must contain a table alias in the string.
     */
    public function testConstructorWithIncorrectCountField()
    {
        $qb = $this->createMock(QueryBuilder::class);
        new DoctrineDBALAdapter($qb, 'somefield');
    }

    public function testGetQueryBuilder()
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('getType')->willReturn(QueryBuilder::SELECT);
        $adapter = new DoctrineDBALAdapter($qb, 'table.somefield');

        $this->assertInstanceOf(QueryBuilder::class, $adapter->getQueryBuilder());
    }

    /**
     * @expectedException \LogicException
     * @expectedExceptionMessage Only SELECT queries can be paginated.
     */
    public function testConstructorThrowsAnotherException()
    {
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('getType')->willReturn(QueryBuilder::DELETE);

        new DoctrineDBALAdapter($qb, 'table.somefield');
    }

    public function testGetSlice()
    {
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

    public function testNbResults()
    {
        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())->method('fetchColumn')->with(0)->willReturn([1, 2, 3]);
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('getType')->willReturn(QueryBuilder::SELECT);
        $qb->expects($this->once())->method('select')->willReturn($qb);
        $qb->expects($this->once())->method('orderBy')->willReturn($qb);
        $qb->expects($this->once())->method('setMaxResults')->with(1)->willReturn($qb);
        $qb->expects($this->once())->method('execute')->willReturn($statement);

        $adapter = new DoctrineDBALAdapter($qb, 'table.somefield');
        $result = $adapter->getNbResults();
        $this->assertCount(3, $result);
    }

    public function testNbResultsWithZeroResults()
    {
        $statement = $this->createMock(Statement::class);
        $statement->expects($this->once())->method('fetchColumn')->with(0)->willReturn(null);
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->once())->method('getType')->willReturn(QueryBuilder::SELECT);
        $qb->expects($this->once())->method('select')->willReturn($qb);
        $qb->expects($this->once())->method('orderBy')->willReturn($qb);
        $qb->expects($this->once())->method('setMaxResults')->with(1)->willReturn($qb);
        $qb->expects($this->once())->method('execute')->willReturn($statement);

        $adapter = new DoctrineDBALAdapter($qb, 'table.somefield');
        $result = $adapter->getNbResults();
        $this->assertSame(0, $result);
    }
}
