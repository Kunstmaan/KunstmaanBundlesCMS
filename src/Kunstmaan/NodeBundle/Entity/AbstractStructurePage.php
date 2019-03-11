<?php

namespace Kunstmaan\NodeBundle\Entity;

/**
 * A StructurePage will always be offline and its nodes will never have a slug.
 */
abstract class AbstractStructurePage extends AbstractPage
{
    /**
     * A StructurePage will always be offline.
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
    public function isStructurePage()
    {
        return true;
    }
}
