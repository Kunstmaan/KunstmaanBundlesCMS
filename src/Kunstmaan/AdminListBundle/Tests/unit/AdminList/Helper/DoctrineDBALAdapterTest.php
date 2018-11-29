<?php

namespace Kunstmaan\AdminListBundle\Tests\AdminList;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Statement;
use Kunstmaan\AdminListBundle\Helper\DoctrineDBALAdapter;
use LogicException;
use PHPUnit_Framework_TestCase;

/**
 * Class AdminListTest
 */
class DoctrineDBALAdapterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DoctrineDBALAdapter
     */
    private $adapter;

    /**
     * @var QueryBuilder
     */
    private $qb;

    public function setUp()
    {
        $statement = $this->createMock(Statement::class);
        $statement->expects($this->any())->method('fetchAll')->willReturn([1, 2, 3]);
        $statement->expects($this->any())->method('fetchColumn')->willReturn([1, 2, 3]);
        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->any())->method('setMaxResults')->willReturn($qb);
        $qb->expects($this->any())->method('setFirstResult')->willReturn($qb);
        $qb->expects($this->any())->method('select')->willReturn($qb);
        $qb->expects($this->any())->method('orderBy')->willReturn($qb);
        $qb->expects($this->any())->method('execute')->willReturn($statement);
        $this->qb = $qb;
    }

    public function testGetQueryBuilder()
    {
        $this->qb->expects($this->any())->method('getType')->willReturn(QueryBuilder::SELECT);
        $this->adapter = new DoctrineDBALAdapter($this->qb, 'table.somefield');
        $this->assertInstanceOf(QueryBuilder::class, $this->adapter->getQueryBuilder());
    }

    public function testConstructorThrowsException()
    {
        $this->expectException(LogicException::class);
        $this->adapter = new DoctrineDBALAdapter($this->qb, 'somefield');
    }

    /**
     * @throws \ReflectionException
     */
    public function testConstructorThrowsAnotherException()
    {
        $this->qb->expects($this->any())->method('getType')->willReturn(QueryBuilder::DELETE);

        $this->expectException(LogicException::class);

        $this->adapter = new DoctrineDBALAdapter($this->qb, 'table.somefield');
    }

    public function testGetSlice()
    {
        $this->qb->expects($this->any())->method('getType')->willReturn(QueryBuilder::SELECT);
        $this->adapter = new DoctrineDBALAdapter($this->qb, 'table.somefield');
        $result = $this->adapter->getSlice(0, 3);
        $this->assertCount(3, $result);
    }

    public function testNbResults()
    {
        $this->qb->expects($this->any())->method('getType')->willReturn(QueryBuilder::SELECT);
        $this->adapter = new DoctrineDBALAdapter($this->qb, 'table.somefield');
        $result = $this->adapter->getNbResults();
        $this->assertCount(3, $result);
    }
}
