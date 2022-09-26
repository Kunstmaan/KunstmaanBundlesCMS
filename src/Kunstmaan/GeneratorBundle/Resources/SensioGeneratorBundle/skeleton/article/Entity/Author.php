<?php

namespace {{ namespace }}\Entity;

use {{ namespace }}\Form\{{ entity_class }}AuthorAdminType;
use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractAuthor;

{% if canUseEntityAttributes %}
#[ORM\Entity]
#[ORM\Table(name: '{{ prefix }}{{ entity_class|lower }}_authors')]
{% else %}
/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_authors")
 */
{% endif %}
class {{ entity_class }}Author extends AbstractAuthor
{
    public function getAdminType(): string
    {
        return {{ entity_class }}AuthorAdminType::class;
    }
}
