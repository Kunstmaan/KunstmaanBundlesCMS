<?php

namespace Kunstmaan\MediaPagePartBundle\Form\Type;

class CurrentValueContainer  {
    private $currentValue;

    public function getCurrentValue() {
        return $this->currentValue;
    }

    public function setCurrentValue($currentValue) {
        $this->currentValue = $currentValue;
    }
}
