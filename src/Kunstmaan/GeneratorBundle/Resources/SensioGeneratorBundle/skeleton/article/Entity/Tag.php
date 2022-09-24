<?php

namespace {{ namespace }}\Entity;

use {{ namespace }}\Form\{{ entity_class }}TagAdminType;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Kunstmaan\ArticleBundle\Entity\AbstractTag;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}{{ entity_class|lower }}_tags')]
#[ORM\UniqueConstraint(name: '{{ entity_class|lower }}_tag_name_idx', columns: ['name'])]
#[Gedmo\SoftDeleteable(fieldName: 'deletedAt')]
{% else %}
/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_tags", uniqueConstraints={@ORM\UniqueConstraint(name="{{ entity_class|lower }}_tag_name_idx", columns={"name"})})
 * @Gedmo\SoftDeleteable(fieldName="deletedAt")
 */
{% endif %}
class {{ entity_class }}Tag extends AbstractTag
{
    public function getAdminType(): string
    {
        return {{ entity_class }}TagAdminType::class;
    }
}
