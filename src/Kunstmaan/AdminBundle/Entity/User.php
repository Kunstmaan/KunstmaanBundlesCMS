<?php

namespace Kunstmaan\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\AdminBundle\Repository\UserRepository;

/**
 * @ORM\Entity(repositoryClass="Kunstmaan\AdminBundle\Repository\UserRepository")
 * @ORM\Table(name="kuma_users")
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'kuma_users')]
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
}
