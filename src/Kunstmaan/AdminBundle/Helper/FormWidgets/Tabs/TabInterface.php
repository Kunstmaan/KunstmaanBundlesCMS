<?php

namespace Kunstmaan\AdminBundle\Helper\FormWidgets\Tabs;

use Kunstmaan\AdminBundle\Helper\FormWidgets\FormWidgetInterface;

/**
 * A tab can be added to the TabPane and show fields or other information of a certain entity
 */
interface TabInterface extends FormWidgetInterface
{
    /**
     * @return string
     */
    public function getTitle();
}
