<?php

namespace Kunstmaan\ArticleBundle\Tests\Entity;

use Kunstmaan\ArticleBundle\Entity\AbstractArticleOverviewPage;
use PHPUnit\Framework\TestCase;

class AbstractArticleOverviewPageTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new class extends AbstractArticleOverviewPage {
            public function getArticleRepository($em)
            {
                return null;
            }
        };
        $this->assertEquals('@KunstmaanArticle/AbstractArticleOverviewPage/view.html.twig', $entity->getDefaultView());
        $this->assertIsArray($entity->getPossibleChildTypes());
        $this->assertIsArray($entity->getPagePartAdminConfigurations());
    }
}
