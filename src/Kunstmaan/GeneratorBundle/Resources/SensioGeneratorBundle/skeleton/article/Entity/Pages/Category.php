<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractCategory;
use {{ namespace }}\Form\{{ entity_class }}CategoryAdminType;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_categorys", uniqueConstraints={@ORM\UniqueConstraint(name="{{ entity_class|lower }}_category_name_idx", columns={"name"})})
 */
class {{ entity_class }}Category extends AbstractCategory
{
    public function getAdminType(): string
    {
        return {{ entity_class }}CategoryAdminType::class;
    }
}
