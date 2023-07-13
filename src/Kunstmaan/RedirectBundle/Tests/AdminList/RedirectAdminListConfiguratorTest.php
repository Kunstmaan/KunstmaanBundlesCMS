<?php

namespace Kunstmaan\RedirectBundle\Tests\AdminList;

use Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface;
use Kunstmaan\AdminListBundle\AdminList\FilterBuilder;
use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator;
use PHPUnit\Framework\TestCase;

class RedirectAdminListConfiguratorTest extends TestCase
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var AclHelper
     */
    protected $aclHelper;

    /**
     * @var RedirectAdminListConfigurator
     */
    protected $object;

    protected function setUp(): void
    {
        $domainConfiguration = $this->getMockBuilder(DomainConfigurationInterface::class)
            ->disableOriginalConstructor()->getMock();

        $this->em = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()->getMock();
        $this->aclHelper = $this->getMockBuilder(AclHelper::class)
            ->disableOriginalConstructor()->getMock();

        $this->object = new RedirectAdminListConfigurator($this->em, $this->aclHelper, $domainConfiguration);
    }

    public function testBuildFields()
    {
        $this->object->buildFields();
        $fields = $this->object->getFields();
        $this->assertCount(4, $fields);
        $fieldNames = array_map(
            fn(Field $field) => $field->getName(),
            $fields
        );
        $this->assertSame(['origin', 'target', 'permanent', 'note'], $fieldNames);
    }

    public function testBuildFilters()
    {
        $filterBuilder = $this->createMock(FilterBuilder::class);
        $filterBuilder
            ->expects($this->exactly(4))
            ->method('add')
            ->withConsecutive(
                [$this->equalTo('origin')],
                [$this->equalTo('target')],
                [$this->equalTo('permanent')],
                [$this->equalTo('note')]
            );
        $this->object->setFilterBuilder($filterBuilder);
        $this->object->buildFilters();
    }

    public function testGetBundleName()
    {
        $this->assertSame('KunstmaanRedirectBundle', $this->object->getBundleName());
    }

    public function testGetEntityName()
    {
        $this->assertSame('Redirect', $this->object->getEntityName());
    }
}
