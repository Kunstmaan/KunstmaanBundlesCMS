<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\Security\Acl;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\QuoteStrategy;
use Doctrine\ORM\Query\AST\FromClause;
use Doctrine\ORM\Query\AST\IdentificationVariableDeclaration;
use Doctrine\ORM\Query\AST\IndexBy;
use Doctrine\ORM\Query\AST\PathExpression;
use Doctrine\ORM\Query\AST\RangeVariableDeclaration;
use Doctrine\ORM\Query\ParserResult;
use Doctrine\ORM\Query\ResultSetMapping;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclWalker;
use PHPUnit_Framework_TestCase;

class AclWalkerTest extends PHPUnit_Framework_TestCase
{
    public function testWalker()
    {
        $range = new RangeVariableDeclaration('someschema', 's');
        $expr = new PathExpression('int', 'id');
        $indexBy = new IndexBy($expr);
        $from = new FromClause([new IdentificationVariableDeclaration($range, $indexBy, [])]);

        $meta = $this->createMock(ClassMetadata::class);
        $strategy = $this->createMock(QuoteStrategy::class);
        $config = $this->createMock(Configuration::class);

        $platform = $this->createMock(AbstractPlatform::class);
        $platform->expects($this->once())->method('appendLockHint')->willReturn($from);

        $conn = $this->createMock(Connection::class);
        $conn->expects($this->once())->method('getDatabasePlatform')->willReturn($platform);

        $em = $this->createMock(EntityManager::class);
        $query = $this->createMock(AbstractQuery::class);
        $mapping = $this->createMock(ResultSetMapping::class);
        $result = $this->createMock(ParserResult::class);

        $meta->expects($this->once())->method('getTableName')->willReturn('sometable');
        $strategy->expects($this->once())->method('getTableName')->willReturn('sometable');
        $config->expects($this->once())->method('getQuoteStrategy')->willReturn($strategy);

        $query->expects($this->once())->method('getEntityManager')->willReturn($em);
        $query->expects($this->exactly(4))->method('getHint')->will($this->onConsecutiveCalls('sometable', 'sometable', 's', null));
        $em->expects($this->once())->method('getConnection')->willReturn($conn);
        $em->expects($this->once())->method('getConfiguration')->willReturn($config);
        $em->expects($this->once())->method('getClassMetaData')->willReturn($meta);
        $result->expects($this->once())->method('getResultSetMapping')->willReturn($mapping);

        $aclWalker = new AclWalker($query, $result, []);
        $sql = $aclWalker->walkFromClause($from);
        $this->assertRegExp('/(JOIN \(\) ta_ ON s0_\.id = ta_.id)$/', $sql);
    }
}
