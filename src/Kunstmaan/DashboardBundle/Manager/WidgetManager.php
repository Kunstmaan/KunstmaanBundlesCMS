<?php

namespace Kunstmaan\DashboardBundle\Manager;

use Kunstmaan\DashboardBundle\Widget\DashboardWidget;

/**
 * Class WidgetManager
 */
class WidgetManager
{
    /**
     * @var DashboardWidget[]
     */
    private $widgets = [];

    /**
     * @param DashboardWidget $widget
     */
    public function addWidget(DashboardWidget $widget)
    {
        $this->widgets[] = $widget;
    }

    /**
     * @return DashboardWidget[]
     */
    public function getWidgets()
    {
        return $this->widgets;
    }

}
