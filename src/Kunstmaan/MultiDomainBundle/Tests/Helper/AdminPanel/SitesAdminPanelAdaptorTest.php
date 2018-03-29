<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Helper\AdminPanel;

use Kunstmaan\MultiDomainBundle\Helper\AdminPanel\SitesAdminPanelAdaptor;
use PHPUnit_Framework_TestCase;

class SitesAdminPanelAdaptorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var SitesAdminPanelAdaptor
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
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
