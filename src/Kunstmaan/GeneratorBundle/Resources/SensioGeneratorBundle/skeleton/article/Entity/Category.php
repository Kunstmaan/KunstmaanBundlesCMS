<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\ArticleBundle\Entity\AbstractCategory;
use {{ namespace }}\Form\{{ entity_class }}CategoryAdminType;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_categories", uniqueConstraints={@ORM\UniqueConstraint(name="{{ entity_class|lower }}_category_name_idx", columns={"name"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class {{ entity_class }}Category extends AbstractCategory
{
    public function getAdminType(): string
    {
        return {{ entity_class }}CategoryAdminType::class;
    }
}
