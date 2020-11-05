<?php

namespace Kunstmaan\ArticleBundle\Tests\Entity;

use Kunstmaan\ArticleBundle\Entity\AbstractArticleOverviewPage;
use PHPUnit\Framework\TestCase;

class ArticleOverViewPage extends AbstractArticleOverviewPage
{
    public function getArticleRepository($em)
    {
        return null;
    }
}

class AbstractArticleOverviewPageTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new ArticleOverViewPage();
        $this->assertEquals('KunstmaanArticleBundle:AbstractArticleOverviewPage:service', $entity->getControllerAction());
        $this->assertEquals('@KunstmaanArticle/AbstractArticleOverviewPage/view.html.twig', $entity->getDefaultView());
        $this->assertInternalType('array', $entity->getPossibleChildTypes());
        $this->assertInternalType('array', $entity->getPagePartAdminConfigurations());
    }
}
