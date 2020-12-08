<?php

namespace Kunstmaan\DashboardBundle\Manager;

use Kunstmaan\DashboardBundle\Widget\DashboardWidget;

class WidgetManager
{
    /**
     * @var DashboardWidget[]
     */
    private $widgets = [];

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
