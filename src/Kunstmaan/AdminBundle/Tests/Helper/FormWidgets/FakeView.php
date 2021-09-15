<?php

namespace Kunstmaan\AdminBundle\Tests\Helper\FormWidgets;

use Symfony\Component\Form\FormView;

class FakeView extends FormView
{
    public function offsetSet($name, $value): void
    {
        $this->children[$name] = $value;
    }
}
