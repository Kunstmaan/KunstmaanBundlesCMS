<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User entity
 *
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\UserRepository")
 * @ORM\Table(name="kuma_users")
 */
class User extends BaseUser
{
    /**
     * Get the classname of the formtype.
     *
     * @return string
     */
    public function getFormTypeClass()
    {
        return 'Kunstmaan\AdminBundle\Form\UserType';
    }

    /**
     * Get the classname of the admin list configurator.
     *
     * @return string
     *
     * @deprecated Use the kunstmaan_user_management.user_admin_list_configurator.class parameter instead!
     */
    public function getAdminListConfiguratorClass()
    {
        return 'Kunstmaan\AdminBundle\AdminList\UserAdminListConfigurator';
    }
}
