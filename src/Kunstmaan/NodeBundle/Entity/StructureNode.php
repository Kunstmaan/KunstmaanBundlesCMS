<?php

namespace Kunstmaan\NodeBundle\Entity;

use Kunstmaan\NodeBundle\Entity\AbstractPage;

/**
 * A StructureNode will always be offline and its nodes will never have a slug.
 */
abstract class StructureNode extends AbstractPage
{

    /**
     * A StructureNode will always be offline.
     *
     * @return bool
     */
    public function isOnline()
    {
        return false;
    }


    /**
     * @inheritdoc
     *
     * By default this is true..
     */
    public function isStructureNode()
    {
        return true;
    }

}
