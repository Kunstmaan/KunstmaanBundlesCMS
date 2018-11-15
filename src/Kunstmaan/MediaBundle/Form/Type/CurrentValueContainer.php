<?php

namespace Kunstmaan\MediaBundle\Form\Type;

/**
 * CurrentValueContainer
 */
class CurrentValueContainer
{
    /**
     * @var object
     */
    private $currentValue;

    /**
     * @return object
     */
    public function getCurrentValue()
    {
        return $this->currentValue;
    }

    /**
     * @param object $currentValue
     */
    public function setCurrentValue($currentValue)
    {
        $this->currentValue = $currentValue;
    }
}
