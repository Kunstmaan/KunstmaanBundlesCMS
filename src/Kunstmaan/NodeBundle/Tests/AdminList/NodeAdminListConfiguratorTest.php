<?php

namespace AdminList;

use Doctrine\ORM\EntityManagerInterface;
use Kunstmaan\AdminBundle\Helper\Security\Acl\AclHelper;
use Kunstmaan\AdminBundle\Helper\Security\Acl\Permission\PermissionMap;
use Kunstmaan\NodeBundle\AdminList\NodeAdminListConfigurator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class NodeAdminListConfiguratorTest extends TestCase
{
    private NodeAdminListConfigurator $adminlist;

    protected function setUp(): void
    {
        $this->adminlist = new NodeAdminListConfigurator(
            $this->createMock(EntityManagerInterface::class),
            $this->createMock(AclHelper::class),
            'en',
            PermissionMap::PERMISSION_VIEW,
            $this->createMock(AuthorizationCheckerInterface::class)
        );
    }

    public function testgetPathByConventionWithoutSuffix()
    {
        $this->assertSame('KunstmaanNodeBundle_nodes', $this->adminlist->getPathByConvention());
    }

    public function testgetPathByConventionWithSuffix()
    {
        $this->assertSame('KunstmaanNodeBundle_nodes_examplesuffix', $this->adminlist->getPathByConvention('examplesuffix'));
    }
}
