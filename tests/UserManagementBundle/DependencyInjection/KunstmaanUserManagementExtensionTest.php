<?php

namespace Tests\Kunstmaan\UserManagementBundle\DependencyInjection;

use Kunstmaan\UserManagementBundle\DependencyInjection\KunstmaanUserManagementExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Tests\Kunstmaan\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanUserManagementExtensionTest
 */
class KunstmaanUserManagementExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanUserManagementExtension()];
    }


    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('empty_extension', true);
        $this->load();

        $this->assertContainerBuilderHasParameter('empty_extension', true );
    }
}
