<?php

namespace Kunstmaan\DashboardBundle\Tests\DependencyInjection;

use Kunstmaan\DashboardBundle\DependencyInjection\KunstmaanDashboardExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Kunstmaan\AdminBundle\Tests\AbstractPrependableExtensionTestCase;

/**
 * Class KunstmaanDashboardExtensionTest
 */
class KunstmaanDashboardExtensionTest extends AbstractPrependableExtensionTestCase
{
    /**
     * @return ExtensionInterface[]
     */
    protected function getContainerExtensions()
    {
        return [new KunstmaanDashboardExtension()];
    }


    public function testCorrectParametersHaveBeenSet()
    {
        $this->load();

        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.widget.googleanalytics.command');
        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.widget.googleanalytics.controller');
        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.googleclient.class');
        $this->assertContainerBuilderHasParameter('kunstmaan_dashboard.googleclienthelper.class');
        $this->assertContainerBuilderHasParameter('google.api.app_name');
    }
}
