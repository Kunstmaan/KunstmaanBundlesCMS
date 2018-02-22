<?php

namespace Tests\Kunstmaan\ArticleBundle\Form;

use Kunstmaan\ArticleBundle\Form\AbstractAuthorAdminType;
use Kunstmaan\ArticleBundle\PagePartAdmin\AbstractArticlePagePagePartAdminConfigurator;
use PHPUnit_Framework_TestCase;
use Symfony\Component\Form\FormBuilder;

/**
 * Class AbstractArticlePagePagePartAdminConfiguratorTest
 * @package Tests\Kunstmaan\ArticleBundle\Form
 */
class AbstractArticlePagePagePartAdminConfiguratorTest extends PHPUnit_Framework_TestCase
{
    public function testGettersAndSetters()
    {
        $entity = new AbstractArticlePagePagePartAdminConfigurator();

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
