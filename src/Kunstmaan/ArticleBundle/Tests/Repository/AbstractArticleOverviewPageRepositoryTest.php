<?php

namespace Kunstmaan\ArticleBundle\Tests\Repository;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use Kunstmaan\ArticleBundle\Repository\AbstractArticleOverviewPageRepository;
use PHPUnit\Framework\TestCase;
use Tests\DoctrineExtensions\Taggable\Fixtures\Article;

class Repo extends AbstractArticleOverviewPageRepository
{
}

class AbstractArticleOverviewPageRepositoryTest extends TestCase
{
    public function testFindActiveOverviewPages()
    {
        $query = $this->createMock(AbstractQuery::class);
        $query->expects($this->once())->method('getResult')->willReturn(['fake', 'data']);

        $qb = $this->createMock(QueryBuilder::class);
        $qb->expects($this->exactly(3))->method('innerJoin')->willReturn($qb);
        $qb->expects($this->once())->method('select')->willReturn($qb);
        $qb->expects($this->once())->method('from')->willReturn($qb);
        $qb->expects($this->once())->method('where')->willReturn($qb);
        $qb->expects($this->once())->method('andWhere')->willReturn($qb);
        $qb->expects($this->exactly(2))->method('setParameter')->willReturn($qb);
        $qb->expects($this->once())->method('getQuery')->willReturn($query);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->once())->method('createQueryBuilder')->willReturn($qb);

        $entity = new Repo($em, new ClassMetadata(Article::class));

        $pages = $entity->findActiveOverviewPages();

        $this->assertCount(2, $pages);
    }
}
