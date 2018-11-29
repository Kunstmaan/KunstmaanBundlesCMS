<?php

namespace Kunstmaan\RedirectBundle\Tests\Entity;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator;
use PHPUnit_Framework_TestCase;

/**
 * Class RedirectAdminListConfiguratorTest
 */
class RedirectAdminListConfiguratorTest extends PHPUnit_Framework_TestCase
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

    protected function setUp()
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
        $this->assertEquals(4, count($fields));
        $fieldNames = array_map(
            function (Field $field) {
                return $field->getName();
            },
            $fields
        );
        $this->assertEquals(array('origin', 'target', 'permanent', 'note'), $fieldNames);
    }

    public function testBuildFilters()
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
