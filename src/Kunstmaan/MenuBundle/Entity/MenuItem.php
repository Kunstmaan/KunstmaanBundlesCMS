<?php

namespace Kunstmaan\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="kuma_menu_item")
 * @ORM\Entity(repositoryClass="Kunstmaan\MenuBundle\Repository\MenuItemRepository")
 * @Gedmo\Tree(type="nested")
 * @Assert\Callback(methods={"validateEntity"})
 */
class MenuItem extends BaseMenuItem
{

}
