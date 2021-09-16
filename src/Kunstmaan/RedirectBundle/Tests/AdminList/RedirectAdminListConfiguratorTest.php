<?php

namespace Kunstmaan\RedirectBundle\Tests\AdminList;

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
        $domainConfiguration = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\DomainConfigurationInterface')
            ->disableOriginalConstructor()->getMock();

        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()->getMock();
        $this->aclHelper = $this->getMockBuilder('Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper')
            ->disableOriginalConstructor()->getMock();

        $this->object = new RedirectAdminListConfigurator($this->em, $this->aclHelper, $domainConfiguration);
    }

    public function testBuildFields()
    {
        $this->object->buildFields();
        $fields = $this->object->getFields();
        $this->assertCount(4, $fields);
        $fieldNames = array_map(
            function (Field $field) {
                return $field->getName();
            },
            $fields
        );
        $this->assertEquals(['origin', 'target', 'permanent', 'note'], $fieldNames);
    }

    public function testBuildFilters()
    {
        $filterBuilder = $this->createMock('Kunstmaan\AdminListBundle\AdminList\FilterBuilder');
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
        $this->assertEquals('KunstmaanRedirectBundle', $this->object->getBundleName());
    }

    public function testGetEntityName()
    {
        $this->assertEquals('Redirect', $this->object->getEntityName());
    }
}
