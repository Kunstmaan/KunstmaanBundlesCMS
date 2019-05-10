<?php

namespace Kunstmaan\AdminBundle\Tests\unit\Helper\FormWidgets;

use Symfony\Component\Form\FormView;

class FakeView extends FormView
{
    /**
     * @param $name
     * @param $value
     */
    public function offsetSet($name, $value)
    {
        $this->children[$name] = $value;
    }
}
