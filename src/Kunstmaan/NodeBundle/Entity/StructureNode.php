<?php

namespace Kunstmaan\NodeBundle\Entity;

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
     * By default this is true..
     *
     * {@inheritdoc}
     */
    public function isStructureNode()
    {
        return true;
    }
}
