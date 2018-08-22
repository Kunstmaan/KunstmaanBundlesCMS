<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * {{ entity }}
 *
 * @ORM\Table(name="{{ prefix }}{{ underscoreName }}")
 * @ORM\Entity
 */
class {{ entity }} extends \Kunstmaan\AdminBundle\Entity\AbstractEntity
{

}
