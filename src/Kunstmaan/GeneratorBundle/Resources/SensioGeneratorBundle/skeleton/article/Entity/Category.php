<?php

namespace {{ namespace }}\Entity;

use {{ namespace }}\Form\{{ entity_class }}CategoryAdminType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\ArticleBundle\Entity\AbstractCategory;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}{{ entity_class|lower }}_categories')]
#[ORM\UniqueConstraint(name: '{{ entity_class|lower }}_category_name_idx', columns: ['name'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
{% else %}
/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_categories", uniqueConstraints={@ORM\UniqueConstraint(name="{{ entity_class|lower }}_category_name_idx", columns={"name"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
{% endif %}
class {{ entity_class }}Category extends AbstractCategory
{
    public function getAdminType(): string
    {
        return {{ entity_class }}CategoryAdminType::class;
    }
}
