<?php

namespace Kunstmaan\AdminListBundle\Tests\DependencyInjection;

use Kunstmaan\AdminListBundle\DependencyInjection\KunstmaanAdminListExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\unit\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanAdminExtensionTest
 */
class KunstmaanAdminListExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanAdminListExtension()];
    }

    public function testCorrectParametersHaveBeenSet()
    {
        $this->container->setParameter('datePicker_startDate', '2014-09-18 10:00:00');
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_adminlist.service.export.class');
    }
}
