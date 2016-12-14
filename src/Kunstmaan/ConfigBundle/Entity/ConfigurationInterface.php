<?php

namespace Kunstmaan\ConfigBundle\Entity;

interface ConfigurationInterface
{
    /**
     * Returns the form type to use for this configuratble entity.
     *
     * @return string
     */
    public function getDefaultAdminType();

    /**
     * The internal name will be used as unique id for the route etc.
     *
     * Use a name with no spaces but with underscores.
     *
     * @return string
     */
    public function getInternalName();

    /**
     * Returns the label for the menu item that will be created.
     *
     * @return string
     */
    public function getLabel();
}
