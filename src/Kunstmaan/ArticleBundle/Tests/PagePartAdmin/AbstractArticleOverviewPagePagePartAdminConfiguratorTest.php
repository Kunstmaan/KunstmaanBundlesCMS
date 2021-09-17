<?php

namespace Kunstmaan\ArticleBundle\Tests\PagePartAdmin;

use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticleOverviewPagePagePartAdminConfigurator;
use PHPUnit\Framework\TestCase;

class AbstractArticleOverviewPagePagePartAdminConfiguratorTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new AbstractArticleOverviewPagePagePartAdminConfigurator();

        $this->assertEquals('Page parts', $entity->getName());
        $this->assertEquals('main', $entity->getContext());
        $this->assertEquals('', $entity->getWidgetTemplate());
        $types = $entity->getPossiblePagePartTypes();
        $this->assertIsArray($types);
        foreach ($types as $type) {
            $this->assertArrayHasKey('name', $type);
            $this->assertArrayHasKey('class', $type);
        }
    }
}
