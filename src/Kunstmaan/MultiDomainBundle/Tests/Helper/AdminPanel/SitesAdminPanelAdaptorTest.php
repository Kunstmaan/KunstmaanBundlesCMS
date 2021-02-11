<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Helper\AdminPanel;

use Kunstmaan\MultiDomainBundle\Helper\AdminPanel\SitesAdminPanelAdaptor;
use PHPUnit\Framework\TestCase;

class SitesAdminPanelAdaptorTest extends TestCase
{
    /**
     * @var SitesAdminPanelAdaptor
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new SitesAdminPanelAdaptor();
    }

    public function testGetAdminPanelActions()
    {
        $actions = $this->object->getAdminPanelActions();
        $this->assertCount(1, $actions);

        $action = $actions[0];
        $this->assertEquals('KunstmaanMultiDomainBundle_switch_site', $action->getUrl()['path']);
    }
}
