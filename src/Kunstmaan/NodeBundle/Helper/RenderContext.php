<?php

namespace Kunstmaan\ViewBundle\Helper;

/**
 *  a context for rendering pages through service methods
 */
class RenderContext extends \ArrayObject
{

    /**
     * @var string
     */
    private $view;

    /**
     * @return string
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param string $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}
