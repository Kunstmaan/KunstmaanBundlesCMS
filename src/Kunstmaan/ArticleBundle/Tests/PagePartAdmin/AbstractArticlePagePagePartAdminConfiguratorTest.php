<?php

namespace Kunstmaan\ArticleBundle\Tests\PagePartAdmin;

use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticlePagePagePartAdminConfigurator;
use PHPUnit\Framework\TestCase;

class AbstractArticlePagePagePartAdminConfiguratorTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new AbstractArticlePagePagePartAdminConfigurator();

        $this->assertSame('Page parts', $entity->getName());
        $this->assertSame('main', $entity->getContext());
        $this->assertSame('', $entity->getWidgetTemplate());
        $types = $entity->getPossiblePagePartTypes();
        $this->assertIsArray($types);
        foreach ($types as $type) {
            $this->assertArrayHasKey('name', $type);
            $this->assertArrayHasKey('class', $type);
        }
    }
}
