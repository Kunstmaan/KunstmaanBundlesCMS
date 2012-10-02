<?php

namespace Kunstmaan\NodeBundle\Entity;

use Kunstmaan\NodeBundle\Entity\HasNodeInterface;

/**
 * The Page Interface
 */
interface PageInterface extends HasNodeInterface
{

    /**
     * @return array
     */
    public function getPossibleChildPageTypes();

}
