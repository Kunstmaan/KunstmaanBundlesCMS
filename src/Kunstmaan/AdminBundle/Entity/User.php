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
     */
    public function getAdminListConfiguratorClass()
    {
        return 'Kunstmaan\AdminBundle\AdminList\UserAdminListConfigurator';
    }

    /**
     * @ORM\Column(type="string", name="locale_admin", length=2)
     */
    protected $localeAdmin;

    /**
     * Get localeAdmin
     *
     * @return string
     */
    public function getlocaleAdmin()
    {
        return $this->localeAdmin;
    }

    /**
     * Set localeAdmin
     *
     * @param string $localeAdmin
     *
     * @return User
     */
    public function setlocaleAdmin($localeAdmin)
    {
        $this->localeAdmin = $localeAdmin;

        return $this;
    }
}
