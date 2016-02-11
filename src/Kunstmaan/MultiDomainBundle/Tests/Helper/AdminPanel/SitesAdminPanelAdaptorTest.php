<?php

namespace Kunstmaan\MultiDomainBundle\Tests\Helper\AdminPanel;

use Kunstmaan\MultiDomainBundle\Helper\AdminPanel\SitesAdminPanelAdaptor;

class SitesAdminPanelAdaptorTest extends \PHPUnit_Framework_TestCase
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

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers Kunstmaan\MultiDomainBundle\Helper\AdminPanel\SitesAdminPanelAdaptor::getAdminPanelActions
     * @covers Kunstmaan\MultiDomainBundle\Helper\AdminPanel\SitesAdminPanelAdaptor::getSiteSwitcherAction
     */
    public function testGetAdminPanelActions()
    {
        $actions = $this->object->getAdminPanelActions();
        $this->assertCount(1, $actions);

        $action = $actions[0];
        $this->assertEquals('KunstmaanMultiDomainBundle_switch_site', $action->getUrl()['path']);
    }
}
