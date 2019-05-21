<?php

namespace Kunstmaan\ArticleBundle\Tests\Form;

use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticlePagePagePartAdminConfigurator;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractArticlePagePagePartAdminConfiguratorTest
 */
class AbstractArticlePagePagePartAdminConfiguratorTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new AbstractArticlePagePagePartAdminConfigurator();

        $this->assertEquals('Page parts', $entity->getName());
        $this->assertEquals('main', $entity->getContext());
        $this->assertEquals('', $entity->getWidgetTemplate());
        $types = $entity->getPossiblePagePartTypes();
        $this->assertInternalType('array', $types);
        foreach ($types as $type) {
            $this->assertArrayHasKey('name', $type);
            $this->assertArrayHasKey('class', $type);
        }
    }
}
