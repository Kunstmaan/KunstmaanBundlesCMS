<?php

namespace Kunstmaan\ArticleBundle\Tests\Entity;

use DateTime;
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
        $entity->setDate(new DateTime());
        $entity->setTitle('NASA');
        $entity->setPageTitle('To infinty and beyond');
        $entity->setSummary('blah');

        $this->assertEquals(666, $entity->getId());
        $this->assertEquals('blah', $entity->getSummary());
        $this->assertEquals('To infinty and beyond', $entity->getPageTitle());
        $this->assertEquals('NASA', $entity->getTitle());
        $this->assertInstanceOf(DateTime::class, $entity->getDate());
        $this->assertEquals(AbstractArticlePageAdminType::class, $entity->getAdminType());
        $this->assertIsArray($entity->getPossibleChildTypes());
        $this->assertIsArray($entity->getPagePartAdminConfigurations());
    }
}
