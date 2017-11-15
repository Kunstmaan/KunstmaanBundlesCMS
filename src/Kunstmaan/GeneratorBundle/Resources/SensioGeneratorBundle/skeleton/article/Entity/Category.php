<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\ArticleBundle\Entity\AbstractCategory;
use Symfony\Component\Form\AbstractType;
use {{ namespace }}\Form\{{ entity_class }}CategoryAdminType;

/**
 * The category for a {{ entity_class }}
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_categorys", uniqueConstraints={@ORM\UniqueConstraint(name="name_idx", columns={"name"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class {{ entity_class }}Category extends AbstractCategory
{
    /**
     * Returns the default backend form type for this page
     *
     * @return string
     */
    public function getAdminType()
    {
        return {{ entity_class }}CategoryAdminType::class;
    }
}
