<?php

namespace Kunstmaan\RedirectBundle\Tests\unit\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator;
use PHPUnit\Framework\TestCase;

/**
 * Class RedirectAdminListConfiguratorTest
 */
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
        $domainConfiguration = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface')
            ->disableOriginalConstructor()->getMock();

        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $this->aclHelper = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper')
            ->disableOriginalConstructor()->getMock();

        $this->object = new RedirectAdminListConfigurator($this->em, $this->aclHelper, $domainConfiguration);
    }

    public function testBuildFields(): void
    {
        $this->object->buildFields();
        $fields = $this->object->getFields();
        $this->assertCount(5, $fields);
        $fieldNames = array_map(
            function (Field $field) {
                return $field->getName();
            },
            $fields
        );
        $this->assertEquals(['origin', 'target', 'permanent', 'note', 'isAutoRedirect'], $fieldNames);
    }

    public function testBuildFilters(): void
    {
        $filterBuilder = $this->createMock('Kunstmaan\AdminListBundle\AdminList\FilterBuilder');
        $filterBuilder
            ->expects($this->at(0))
            ->method('add')
            ->with('origin');
        $filterBuilder
            ->expects($this->at(1))
            ->method('add')
            ->with('target');
        $filterBuilder
            ->expects($this->at(2))
            ->method('add')
            ->with('permanent');
        $filterBuilder
            ->expects($this->at(3))
            ->method('add')
            ->with('note');
        $filterBuilder
            ->expects($this->at(4))
            ->method('add')
            ->with('isAutoRedirect');
        $this->object->setFilterBuilder($filterBuilder);
        $this->object->buildFilters();
    }

    public function testGetBundleName(): void
    {
        $this->assertEquals('KunstmaanRedirectBundle', $this->object->getBundleName());
    }

    public function testGetEntityName(): void
    {
        $this->assertEquals('Redirect', $this->object->getEntityName());
    }
}
