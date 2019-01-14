<?php

namespace Kunstmaan\ArticleBundle\Tests\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticleAuthorAdminListConfigurator;
use PHPUnit_Framework_TestCase;

/**
 * Class AbstractArticleAuthorAdminListConfiguratorTest
 */
class AbstractArticleAuthorAdminListConfiguratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractArticleAuthorAdminListConfigurator
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $em = $this->createMock(EntityManager::class);
        $em->expects($this->any())
            ->method($this->anything())
            ->willReturn($em);

        $acl = $this->createMock(AclHelper::class);

        $this->object = new AbstractArticleAuthorAdminListConfigurator($em, $acl, 'nl', 'admin');
    }

    public function testGetters()
    {
        $this->assertEquals('KunstmaanArticleBundle', $this->object->getBundleName());
        $this->assertEquals('AbstractArticleAuthor', $this->object->getEntityName());
    }

    public function testBuildFields()
    {
        $this->object->buildFields();
        $this->object->buildFilters();
        $fields = $this->object->getFields();
        $this->assertCount(2, $fields);
    }
}
