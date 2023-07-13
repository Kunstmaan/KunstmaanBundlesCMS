<?php

namespace Kunstmaan\ArticleBundle\Tests\Entity;

use Kunstmaan\ArticleBundle\Entity\AbstractArticlePage;
use Kunstmaan\ArticleBundle\Form\AbstractArticlePageAdminType;
use PHPUnit\Framework\TestCase;

class ArticlePage extends AbstractArticlePage
{
}

class AbstractArticlePageTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new ArticlePage();
        $entity->setId(666);
        $entity->setDate(new \DateTime());
        $entity->setTitle('NASA');
        $entity->setPageTitle('To infinty and beyond');
        $entity->setSummary('blah');

        $this->assertSame(666, $entity->getId());
        $this->assertSame('blah', $entity->getSummary());
        $this->assertSame('To infinty and beyond', $entity->getPageTitle());
        $this->assertSame('NASA', $entity->getTitle());
        $this->assertInstanceOf(\DateTime::class, $entity->getDate());
        $this->assertSame(AbstractArticlePageAdminType::class, $entity->getAdminType());
        $this->assertIsArray($entity->getPossibleChildTypes());
        $this->assertIsArray($entity->getPagePartAdminConfigurations());
    }
}
