<?php

namespace Kunstmaan\AdminListBundle\Tests\DependencyInjection;

use Kunstmaan\AdminListBundle\DependencyInjection\KunstmaanAdminListExtension;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class KunstmaanAdminListExtensionTest extends AbstractExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions(): array
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
