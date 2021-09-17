<?php

namespace Kunstmaan\ArticleBundle\Tests\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticleAuthorAdminListConfigurator;
use PHPUnit\Framework\TestCase;

class AbstractArticleAuthorAdminListConfiguratorTest extends TestCase
{
    /**
     * @var AbstractArticleAuthorAdminListConfigurator
     */
    protected $object;

    protected function setUp(): void
    {
        $em = $this->createMock(EntityManager::class);
        $em->expects($this->any())
            ->method($this->anything())
            ->willReturn($em);

        $acl = $this->createMock(AclHelper::class);

        $this->object = new AbstractArticleAuthorAdminListConfigurator($em, $acl, 'nl');
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
