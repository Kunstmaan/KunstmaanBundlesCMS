<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\ArticleBundle\Entity\AbstractTag;
use Symfony\Component\Form\AbstractType;
use {{ namespace }}\Form\{{ entity_class }}TagAdminType;

/**
 * The tag for a {{ entity_class }}
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_tags", uniqueConstraints={@ORM\UniqueConstraint(name="name_idx", columns={"name"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
class {{ entity_class }}Tag extends AbstractTag
{
    /**
     * Returns the default backend form type for this page
     *
     * @return string
     */
    public function getAdminType()
    {
        return {{ entity_class }}TagAdminType::class;
    }
}
