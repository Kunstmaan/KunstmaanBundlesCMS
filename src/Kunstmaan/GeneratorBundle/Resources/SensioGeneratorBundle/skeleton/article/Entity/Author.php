<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractAuthor;
use {{ namespace }}\Form\{{ entity_class }}AuthorAdminType;

/**
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_authors")
 */
class {{ entity_class }}Author extends AbstractAuthor
{
    public function getAdminType(): string
    {
        return {{ entity_class }}AuthorAdminType::class;
    }
}
