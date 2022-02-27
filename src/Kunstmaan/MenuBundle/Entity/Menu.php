<?php

namespace Kunstmaan\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="kuma_menu")
 * @ORM\Entity()
 */
#[ORM\Entity]
#[ORM\Table(name: 'kuma_menu')]
class Menu extends BaseMenu
{
}
