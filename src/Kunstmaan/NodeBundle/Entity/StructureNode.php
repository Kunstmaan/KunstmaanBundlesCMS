<?php

namespace Kunstmaan\NodeBundle\Entity;

/**
 * @deprecated Using the StructureNode class is deprecated in KunstmaanNodeBundle 5.2 and will be removed in KunstmaanNodeBundle 6.0. use AbstractStructurePage.
 */
abstract class StructureNode extends StructurePage
{
    /**
     * By default this is true..
     *
     * @deprecated Using the isStructureNode method is deprecated in KunstmaanNodeBundle 5.2 and will be removed in KunstmaanNodeBundle 6.0. use isStructurePage.
     */
    public function isStructureNode()
    {
        return true;
    }
}
