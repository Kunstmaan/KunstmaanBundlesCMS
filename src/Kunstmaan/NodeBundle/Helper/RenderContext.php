<?php

namespace Kunstmaan\NodeBundle\Helper;

/**
 * A context for rendering pages through service methods
 */
class RenderContext extends \ArrayObject
{
    /**
     * @var string
     */
    private $view;

    /**
     * Get view.
     *
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set view.
     *
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}
