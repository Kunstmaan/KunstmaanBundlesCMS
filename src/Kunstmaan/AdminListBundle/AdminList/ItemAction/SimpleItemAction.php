<?php

namespace Kunstmaan\AdminListBundle\AdminList\ItemAction;

/**
 * The simple item action is a default implementation of the item action interface, this can be used
 * in very simple use cases.
 */
class SimpleItemAction implements ItemActionInterface
{
    /**
     * @var callable
     */
    private $routerGenerator;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $label;

    /**
     * @var null|string
     */
    private $template;

    /**
     * @param callable $routerGenerator the generator used to generate the url of an item, when generating the item will
     *                                  be provided
     * @param string   $icon            The icon
     * @param string   $label           The label
     * @param string   $template        The template
     */
    public function __construct($routerGenerator, $icon, $label, $template = null)
    {
        $this->routerGenerator = $routerGenerator;
        $this->icon = $icon;
        $this->label = $label;
        $this->template = $template;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getUrlFor($item)
    {
        $routeGenerator = $this->routerGenerator;
        if (is_callable($routeGenerator)) {
            return $routeGenerator($item);
        }

        return null;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item)
    {
        return $this->icon;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getLabelFor($item)
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
