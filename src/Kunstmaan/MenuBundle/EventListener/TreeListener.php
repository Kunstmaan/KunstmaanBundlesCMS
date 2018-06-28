<?php

namespace Kunstmaan\MenuBundle\EventListener;

use Doctrine\Common\Persistence\ObjectManager;
use Gedmo\Tree\TreeListener as GedmoTreeListener;
use Kunstmaan\MenuBundle\EventListener\Strategy\ORM\Nested;

/**
 * Class TreeListener
 *
 * Setup in services.yml
 *       gedmo.listener.tree:
 *           class: Kunstmaan\MenuBundle\EventListener\TreeListener
 *          tags:
 *          - { name: doctrine.event_subscriber, connection: default }
 *          calls:
 *          - [ setAnnotationReader, [ "@annotation_reader" ] ]
 */
class TreeListener extends GedmoTreeListener
{
    private $strategyInstances = [];
    private $strategies = [];

    /**
     * Get the used strategy for tree processing
     *
     * @param ObjectManager $om
     * @param string        $class
     *
     * @return Nested
     */
    public function getStrategy(ObjectManager $om, $class)
    {
        $nested = new Nested($this);
        $this->strategies[$class] = 'nested';
        $this->strategyInstances['nested'] = $nested;
        return $nested;
    }
}