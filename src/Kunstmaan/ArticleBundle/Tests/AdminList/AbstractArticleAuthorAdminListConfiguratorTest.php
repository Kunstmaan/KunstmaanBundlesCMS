<?php

namespace Kunstmaan\ArticleBundle\Tests\AdminList;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\ArticleBundle\AdminList\AbstractArticleAuthorAdminListConfigurator;
use Kunstmaan\ArticleBundle\Entity\AbstractAuthor;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

class AbstractArticleAuthorAdminListConfiguratorTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @var AbstractArticleAuthorAdminListConfigurator
     */
    protected $object;

    protected function setUp(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->any())
            ->method($this->anything())
            ->willReturn($em);

        $acl = $this->createMock(AclHelper::class);

        $this->object = new AbstractArticleAuthorAdminListConfigurator($em, $acl, 'nl');
    }

    /**
     * @group legacy
     */
    public function testDeprecatedGetters()
    {
        $this->expectDeprecation('Since kunstmaan/article-bundle 6.4: Method "Kunstmaan\ArticleBundle\AdminList\AbstractArticleAuthorAdminListConfigurator::getBundleName" deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.');
        $this->expectDeprecation('Since kunstmaan/article-bundle 6.4: Method "Kunstmaan\ArticleBundle\AdminList\AbstractArticleAuthorAdminListConfigurator::getEntityName" deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.');

        $this->assertEquals('KunstmaanArticleBundle', $this->object->getBundleName());
        $this->assertEquals('AbstractArticleAuthor', $this->object->getEntityName());
    }

    public function testGetters()
    {
        $this->assertEquals(AbstractAuthor::class, $this->object->getEntityClass());
    }

    public function testBuildFields()
    {
        $this->object->buildFields();
        $this->object->buildFilters();
        $fields = $this->object->getFields();
        $this->assertCount(2, $fields);
    }
}
