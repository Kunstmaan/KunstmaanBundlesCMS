<?php

namespace {{ namespace }}\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kunstmaan\ArticleBundle\Entity\AbstractAuthor;
use {{ namespace }}\Form\{{ entity_class }}AuthorAdminType;
use Symfony\Component\Form\AbstractType;

/**
 * The author for a {{ entity_class }}
 *
 * @ORM\Entity()
 * @ORM\Table(name="{{ prefix }}{{ entity_class|lower }}_authors")
 */
class {{ entity_class }}Author extends AbstractAuthor
{
    /**
     * Returns the default backend form type for this page
     *
     * @return AbstractType
     */
    public function getAdminType()
    {
        return new {{ entity_class }}AuthorAdminType();
    }
}