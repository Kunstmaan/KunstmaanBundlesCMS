<?php

namespace Kunstmaan\DashboardBundle\Tests\Widget;

use Kunstmaan\DashboardBundle\Widget\DashboardWidget;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;
use Symfony\Component\DependencyInjection\Container;

class DashboardWidgetTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testConstructorDeprecations()
    {
        $this->expectDeprecation('The "$container" argument of "Kunstmaan\DashboardBundle\Widget\DashboardWidget::__construct" is deprecated since KunstmaanDashboardBundle 5.9 and will be removed in KunstmaanDashboardBundle 6.0.');
        $this->expectDeprecation('Passing a command classname for the "$command" argument in "Kunstmaan\DashboardBundle\Widget\DashboardWidget::__construct" is deprecated since KunstmaanDashboardBundle 5.9 and will not be allowed in KunstmaanDashboardBundle 6.0. Pass a command name instead.');

        new DashboardWidget(TestCommandStub::class, 'SomeController', new Container());
    }
}

class TestCommandStub
{
    public function setContainer()
    {
    }
}
