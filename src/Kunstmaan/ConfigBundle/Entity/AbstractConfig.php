<?php

namespace Kunstmaan\ConfigBundle\Entity;

use Kunstmaan\AdminBundle\Entity\AbstractEntity;

/**
 * The Abstract class for the config entities
 */
abstract class AbstractConfig extends AbstractEntity implements ConfigurationInterface
{
    public function getRoles()
    {
        return array('ROLE_SUPER_ADMIN');
    }
}
