<?php

namespace Kunstmaan\FixturesBundle\Populator;

use Doctrine\Common\Collections\ArrayCollection;
use Kunstmaan\FixturesBundle\Populator\Methods\MethodInterface;

class Populator
{
    /**
     * @var MethodInterface[]
     */
    private $populators;

    public function __construct()
    {
        $this->populators = new ArrayCollection();
    }

    public function populate($entity, $data)
    {
        foreach ($data as $property => $value) {
            foreach ($this->populators as $populator) {
                if ($populator->canSet($entity, $property, $value)) {
                    if ($value instanceof \Kunstmaan\FixturesBundle\Loader\Fixture) {
                        $populator->set($entity, $property, $value->getEntity());
                    } elseif (is_array($value)) {
                        foreach ($value as &$item) {
                            if ($item instanceof \Kunstmaan\FixturesBundle\Loader\Fixture) {
                                $item = $item->getEntity();
                            }
                        }
                        $populator->set($entity, $property, $value);
                    } else {
                        $populator->set($entity, $property, $value);
                    }

                    break;
                }
            }
        }
    }

    public function addPopulator(MethodInterface $populator, $alias)
    {
        $this->populators->set($alias, $populator);

        return $this;
    }
}
