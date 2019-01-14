<?php

namespace Kunstmaan\ArticleBundle\Tests\Form;

use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticleOverviewPagePagePartAdminConfigurator;
use PHPUnit_Framework_TestCase;

/**
 * Class AbstractArticleOverviewPagePagePartAdminConfiguratorTest
 */
class AbstractArticleOverviewPagePagePartAdminConfiguratorTest extends PHPUnit_Framework_TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new AbstractArticleOverviewPagePagePartAdminConfigurator();

        $this->assertEquals('Page parts', $entity->getName());
        $this->assertEquals('main', $entity->getContext());
        $this->assertEquals('', $entity->getWidgetTemplate());
        $types = $entity->getPossiblePagePartTypes();
        $this->assertTrue(is_array($types));
        foreach ($types as $type) {
            $this->assertArrayHasKey('name', $type);
            $this->assertArrayHasKey('class', $type);
        }
    }
}
