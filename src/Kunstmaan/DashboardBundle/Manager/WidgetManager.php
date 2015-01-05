<?php

namespace Kunstmaan\DashboardBundle\Manager;


use Kunstmaan\DashboardBundle\Widget\DashboardWidget;

class WidgetManager
{

    /**
     * @var DashboardWidget[]
     */
    private $widgets = array();

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
