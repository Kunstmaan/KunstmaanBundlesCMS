<?php

namespace Kunstmaan\RedirectBundle\Tests\AdminList;

use Doctrine\ORM\EntityManager;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminListBundle\AdminList\Field;
use Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator;
use Kunstmaan\RedirectBundle\Entity\Redirect;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

class RedirectAdminListConfiguratorTest extends TestCase
{
    use ExpectDeprecationTrait;

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

    /**
     * @group legacy
     */
    public function testGetBundleName()
    {
        $this->expectDeprecation('Since kunstmaan/redirect-bundle 6.4: The "Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator::getBundleName" method is deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.');

        $this->assertEquals('KunstmaanRedirectBundle', $this->object->getBundleName());
    }

    /**
     * @group legacy
     */
    public function testGetEntityName()
    {
        $this->expectDeprecation('Since kunstmaan/redirect-bundle 6.4: The "Kunstmaan\RedirectBundle\AdminList\RedirectAdminListConfigurator::getEntityName" method is deprecated and will be removed in 7.0. Use the "getEntityClass" method instead.');

        $this->assertEquals('Redirect', $this->object->getEntityName());
    }

    public function testGetEntityClass()
    {
        $this->assertSame(Redirect::class, $this->object->getEntityClass());
    }
}
